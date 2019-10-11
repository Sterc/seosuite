<?php

/**
 * The main SeoSuite service class.
 *
 * @package seosuite
 */
class SeoSuite
{
    /**
     * @var modX
     */
    public $modx;

    /**
     * @var string
     */
    public $namespace = 'seosuite';

    /**
     * @var array
     */
    public $options = [];

    /**
     * Holds all plugins.
     *
     * @var array $plugins
     */
    protected $plugins = [];

    public function __construct(modX &$modx, array $options = [])
    {
        $this->modx      =& $modx;
        $this->namespace = $this->getOption('namespace', $options, 'seosuite');

        $corePath = $this->getOption(
            'core_path',
            $options,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/seosuite/'
        );
        $assetsPath = $this->getOption(
            'assets_path',
            $options,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/seosuite/'
        );
        $assetsUrl = $this->getOption(
            'assets_url',
            $options,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/seosuite/'
        );

        $this->modx->lexicon->load('seosuite:default');

        /* Check for valid version of SeoTab on load */
        if ($this->getSeoTabVersion() === false) {
            $seoTabNotice = $this->modx->lexicon('seosuite.seotab.notfound');
        } else if ($this->getSeoTabVersion() < '2.0.0-pl') {
            $seoTabNotice = $this->modx->lexicon('seosuite.seotab.versioninvalid');
        } else {
            $seoTabNotice = '';
        }

        /* Loads some default paths for easier management. */
        $this->options = array_merge([
            'namespace'     => $this->namespace,
            'corePath'      => $corePath,
            'modelPath'     => $corePath . 'model/',
            'chunksPath'    => $corePath . 'elements/chunks/',
            'snippetsPath'  => $corePath . 'elements/snippets/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath'    => $assetsPath,
            'assetsUrl'     => $assetsUrl,
            'jsUrl'         => $assetsUrl . 'js/',
            'cssUrl'        => $assetsUrl . 'css/',
            'connectorUrl'  => $assetsUrl . 'connector.php',
            'seoTabNotice'  => $seoTabNotice
        ], $options);

        /* Retrieve all plugin classes. */
        $this->setPlugins();

        $this->modx->addPackage('seosuite', $this->getOption('modelPath'));
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = [], $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }

        return $option;
    }

    /**
     * Finds suggested resource(s) to redirect a 404 url to
     * Uses the part after the last / in the url, without querystring
     * Also strips off the extension
     * Example: 'http://test.com/path/awesome-file.html?a=b' becomes 'awesome-file'
     *
     * @param string $url The 404 url
     * @param array $contextSiteUrls An array with site_url => context combinations. If not empty, limits to context
     * @return array An array with modResource objects
     */
    public function findRedirectSuggestions($url, $contextSiteUrls = [])
    {
        $output    = [];
        $parsedUrl = parse_url($url);

        if (isset($parsedUrl['path'])) {
            $pathParts    = explode('/', trim($parsedUrl['path'], '/'));
            $keys         = array_keys($pathParts);
            $searchString = $pathParts[end($keys)];
            $extension    = pathinfo($parsedUrl['path'], PATHINFO_EXTENSION);

            $context = false;
            if (is_array($contextSiteUrls) || is_object($contextSiteUrls)) {
                foreach ($contextSiteUrls as $siteUrl => $ctx) {
                    if (strpos($url, $siteUrl) !== false) {
                        $context = $ctx;
                    }
                }
            }

            if (!empty($extension)) {
                $searchString = str_replace('.' . $extension, '', $searchString);
            }

            $searchWords = $this->splitUrl($searchString);
            $searchWords = $this->filterStopWords($searchWords);
            if (is_array($searchWords) || is_object($searchWords)) {
                foreach ($searchWords as $word) {
                    // Try to find a resource with an exact matching alias
                    // or a resource with matching pagetitle, where non-alphanumeric chars are replaced with space
                    $q = $this->modx->newQuery('modResource');
                    if ($context) {
                        $q->where([
                            'context_key' => $context
                        ]);
                    }

                    $q->where([
                        [
                            'alias:LIKE' => '%' . $word . '%',
                            'OR:pagetitle:LIKE' => '%' . $word . '%'
                        ],
                        [
                            'AND:published:=' => true,
                            'AND:deleted:=' => false
                        ]
                    ]);

                    $excludeWords = $this->getExcludeWords();
                    if (is_array($excludeWords) || is_object($excludeWords)) {
                        foreach ($excludeWords as $excludeWord) {
                            $q->where([
                                'alias:NOT LIKE' => '%' . $excludeWord . '%',
                                'pagetitle:NOT LIKE' => '%' . $excludeWord . '%',
                            ]);
                        }
                    }

                    $q->prepare();
                    if ($results = $this->modx->query($q->toSQL())) {
                        while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                            $output[] = $row['modResource_id'];
                        }
                    }
                }
            }
        }

        $output = array_unique($output);

        return $output;
    }

    /**
     * Split an url string into an array with separate words.
     *
     * @param   string $input
     * @return  array  An array with all the separate words
     */
    public function splitUrl($input)
    {
        return str_word_count(str_replace('-', '_', $input), 1, '1234567890');
    }

    /**
     * Get an array of stopwords from the stopword txt files
     * Uses stopwords from https://github.com/digitalmethodsinitiative/dmi-tcat/tree/master/analysis/common/stopwords
     * Also uses exclude words from system setting 'seosuite.exclude_words'
     *
     * @return  array An array with stopwords
     */
    public function getStopWords()
    {
        $stopwords    = [];
        $stopwordsDir = $this->options['corePath'].'elements/stopwords/';
        if (file_exists($stopwordsDir)) {
            $files = glob($stopwordsDir.'/*.txt');
            foreach ($files as $file) {
                $content = file_get_contents($file);
                if (is_array(explode(PHP_EOL, $content)) && count(explode(PHP_EOL, $content))) {
                    $stopwords = array_merge($stopwords, explode(PHP_EOL, $content));
                }
            }
        }

        $excludeWords = $this->getExcludeWords();
        if (count($excludeWords)) {
            $stopwords = array_merge($stopwords, $excludeWords);
        }

        return $stopwords;
    }

    /**
     * Get exclude words from system setting 'seosuite.exclude_words'
     * @return array
     */
    public function getExcludeWords()
    {
        $output       = [];
        $excludeWords = $this->modx->getOption('seosuite.exclude_words');
        if ($excludeWords) {
            $output = explode(',', $excludeWords);
        }

        return $output;
    }

    /**
     * Remove stop words from url.
     *
     * @param   array $input The input array
     * @return  array $filtered An array with only allowed words from input string
     */
    public function filterStopWords($input)
    {
        $stopwords = $this->getStopWords();
        $filtered  = [];
        if (is_array($input) || is_object($input)) {
            foreach ($input as $word) {
                if (!in_array($word, $stopwords)) {
                    $filtered[] = $word;
                }
            }
        }

        return $filtered;
    }

    /**
     * Adds a redirect to SEOTab for a given resource
     *
     * @param   int $url    The 404 url
     * @param   int $id     The resource id
     * @return  int The id of the seoUrl object, or false if seotab is not installed
     */
    public function addSeoTabRedirect($url, $id)
    {
        $redirect_id = false;
        $url = urlencode($url);

        /* First check for valid version of SeoTab */
        if (!$this->checkSeoTab()) {
            return false;
        }

        $redirect = $this->modx->getObject('seoUrl', ['url' => $url, 'resource' => $id]);
        if (!$redirect) {
            $resource = $this->modx->getObject('modResource', $id);
            if ($resource) {
                $redirect = $this->modx->newObject('seoUrl');
                $data     = [
                    'url'         => $url,
                    'resource'    => $id,
                    'context_key' => $resource->get('context_key'),
                ];

                $redirect->fromArray($data);
                $redirect->save();

                $redirect_id = $redirect->get('id');
            }
        }

        return $redirect_id;
    }

    /**
     * Check if SeoTab is installed and is the minimum correct version
     * @return  boolean
     */
    public function checkSeoTab()
    {
        $stercseo = $this->modx->getService(
            'stercseo',
            'StercSEO',
            $this->modx->getOption(
                'stercseo.core_path',
                null,
                $this->modx->getOption('core_path') . 'components/stercseo/'
            ) . 'model/stercseo/',
            []
        );

        if (!($stercseo instanceof StercSEO)) {
            return false;
        }

        $version = $this->getSeoTabVersion();
        if ($version < '2.0.0-pl') {
            return false;
        }

        return true;
    }

    public function getSeoTabVersion()
    {
        $c = $this->modx->newQuery('transport.modTransportPackage');
        // Using double where clause to group the OR
        $c->where([
            ['package_name' => 'SEO Tab'],
            ['OR:package_name:=' => 'stercseo']
        ]);
        $c->where([
            'installed:IS NOT' => null
        ]);
        $c->sortby('version_major', 'DESC');
        $c->sortby('version_minor', 'DESC');
        $c->sortby('version_patch', 'DESC');

        $c->limit(1);

        $stPackage = $this->modx->getObject('transport.modTransportPackage', $c);
        if ($stPackage) {
            return $stPackage->get('version_major') . '.' . $stPackage->get('version_minor') . '.' . $stPackage->get('version_patch') . '-' . $stPackage->get('release');
        }

        $gitpackagemanagement = $this->modx->getService('gitpackagemanagement', 'GitPackageManagement', $this->modx->getOption('gitpackagemanagement.core_path', null, $this->modx->getOption('core_path') . 'components/gitpackagemanagement/') . 'model/gitpackagemanagement/');
        if (!($gitpackagemanagement instanceof GitPackageManagement)) {
            return false;
        }

        $c = $this->modx->newQuery('GitPackage');
        $c->where([
            'name' => 'StercSEO'
        ]);

        $gitStPackage = $this->modx->getObject('GitPackage', $c);
        if ($gitStPackage) {
            return $gitStPackage->get('version');
        }

        return false;
    }

    /**
     * Gets language strings for use on non-SeoSuite controllers.
     * @return string
     */
    public function getLangs()
    {
        $entries = $this->modx->lexicon->loadCache('seosuite');
        $langs   = 'Ext.applyIf(MODx.lang,' . $this->modx->toJSON($entries) . ');';

        return $langs;
    }

    /**
     * Returns a list of all context site urls (if any).
     *
     * @return array
     */
    public function getSiteUrls()
    {
        $urls = [];

        $q = $this->modx->newQuery('modContextSetting');
        $q->where([
            'key'            => 'site_url',
            'context_key:!=' => 'mgr'
        ]);

        $collection = $this->modx->getCollection('modContextSetting', $q);
        foreach ($collection as $item) {
            $urls[$item->get('value')] = $item->get('context_key');
        }

        return $urls;
    }

    /**
     * Gets a Chunk and caches it; also falls back to file-based templates.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name, $properties = [])
    {
        if (class_exists('pdoTools') && $pdo = $this->modx->getService('pdoTools')) {
            return $pdo->getChunk($name, $properties);
        }

        $chunk = null;
        if (substr($name, 0, 6) === '@CODE:') {
            $content = substr($name, 6);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($content);
        } elseif (!isset($this->chunks[$name])) {
            if (!$this->config['debug']) {
                $chunk = $this->modx->getObject('modChunk', ['name' => $name], true);
            }

            if (empty($chunk)) {
                $chunk = $this->_getTplChunk($name);
                if ($chunk === false) {
                    return false;
                }
            }

            $this->chunks[$name] = $chunk->getContent();
        } else {
            $content = $this->chunks[$name];
            $chunk   = $this->modx->newObject('modChunk');
            $chunk->setContent($content);
        }

        $chunk->setCacheable(false);

        return $chunk->process($properties);
    }

    /**
     * Fire plugins based on event.
     *
     * @param modSystemEvent $event
     * @param array $properties
     *
     * @return bool
     */
    public function firePlugins(modSystemEvent $event, array $properties = [])
    {
        foreach ($this->plugins as $plugin) {
            if (method_exists($plugin, $event->name)) {
                call_user_func_array(
                    [
                        $plugin,
                        $event->name
                    ],
                    [
                        $event,
                        $properties
                    ]
                );
            }
        }

        return true;
    }

    /**
     * Create a list of all plugins.
     */
    protected function setPlugins()
    {
        require_once __DIR__ . '/seosuiteplugin.class.php';

        $pluginsPath = __DIR__ . '/plugins';
        $classSuffix = '.class.php';

        if (file_exists($pluginsPath)) {
            $handle = opendir($pluginsPath);

            while (($file = readdir($handle)) !== false) {
                if (substr($file, -10) === $classSuffix) {
                    $class = str_replace($classSuffix, '', $file);

                    $this->plugins[] = $this->getClass('plugins' . '.' . $class);
                }
            }
        }
    }

    /**
     * Get the class instance.
     *
     * @param string $class
     *
     * @return bool|SitePlugin
     */
    protected function getClass($class, $path = __DIR__ . '/')
    {
        $class = $this->modx->loadClass($class, $path, false, true);
        if (!$class) {
            return false;
        }

        $instance = new $class($this->modx, $this);
        if (!$instance instanceof $class) {
            return false;
        }

        return $instance;
    }
}
