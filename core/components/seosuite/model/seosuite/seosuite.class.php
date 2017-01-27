<?php

/**
 * The main SeoSuite service class.
 *
 * @package seosuite
 */
class SeoSuite
{
    public $modx = null;
    public $namespace = 'seosuite';
    public $cache = null;
    public $options = array();

    public function __construct(modX &$modx, array $options = array())
    {
        $this->modx =& $modx;
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

        /* loads some default paths for easier management */
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'seoTabNotice' => $seoTabNotice
        ), $options);

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
    public function getOption($key, $options = array(), $default = null)
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
     * @return array An array with modResource objects
     */
    public function findRedirectSuggestions($url)
    {
        $output = [];
        $url = parse_url($url);
        if (isset($url['path'])) {
            $pathParts = explode('/', $url['path']);
            $keys = array_keys($pathParts);
            $searchString = $pathParts[end($keys)];
            $extension = pathinfo($url['path'], PATHINFO_EXTENSION);
            if (!empty($extension)) {
                $searchString = str_replace('.'.$extension, '', $searchString);
            }
            $searchWords = $this->splitUrl($searchString);
            $searchWords = $this->filterStopWords($searchWords);
            foreach ($searchWords as $word) {
                // Try to find a resource with an exact matching alias
                // or a resource with matching pagetitle, where non-alphanumeric chars are replaced with space
                $q = $this->modx->newQuery('modResource');
                $q->where(array(
                    array(
                        'alias:LIKE' => '%'.$word.'%',
                        'OR:pagetitle:LIKE' => '%'.$word.'%'
                    ),
                    array(
                        'AND:published:=' => true,
                        'AND:deleted:=' => false
                    )
                ));
                $q->prepare();
                $results = $this->modx->query($q->toSql());
                while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                    $output[] = $row['modResource_id'];
                }
            }
        }
        return $output;
    }

    /**
     * Split an url string into an array with separate words
     * @param   string $input
     * @return  array An array with all the separate words
     */
    public function splitUrl($input)
    {
        return str_word_count(str_replace('-', '_', $input), 1, '1234567890');
    }

    /**
     * Get an array of stopwords from the stopword txt files
     * Uses stopwords from https://github.com/digitalmethodsinitiative/dmi-tcat/tree/master/analysis/common/stopwords
     * @return  array An array with stopwords
     */
    public function getStopWords()
    {
        $stopwords = array();
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
        return $stopwords;
    }

    /**
     * @param   array $input The input array
     * @return  array $filtered An array with only allowed words from input string
     */
    public function filterStopWords($input)
    {
        $stopwords = $this->getStopWords();
        $filtered = array();
        foreach ($input as $word) {
            if (!in_array($word, $stopwords)) {
                $filtered[] = $word;
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
                $data     = array(
                    'url' => $url, 'resource' => $id, 'context_key' => $resource->get('context_key'),
                );
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
        /**
         * Check in transport packages table
         */
        $c = $this->modx->newQuery('transport.modTransportPackage');
        $c->where(
            [
            'workspace' => 1, "(SELECT
            `signature`
            FROM {$this->modx->getTableName('modTransportPackage')} AS `latestPackage`
            WHERE `latestPackage`.`package_name` = `modTransportPackage`.`package_name`
            ORDER BY
                `latestPackage`.`version_major` DESC,
                `latestPackage`.`version_minor` DESC,
                `latestPackage`.`version_patch` DESC,
                IF(`release` = '' OR `release` = 'ga' OR `release` = 'pl','z',`release`) DESC,
                `latestPackage`.`release_index` DESC
                LIMIT 1,1) = `modTransportPackage`.`signature`",
            ]
        );
        $c->where([
            'modTransportPackage.package_name' => 'stercseo',
            'installed:IS NOT' => null
        ]);
        $stPackage = $this->modx->getObject('transport.modTransportPackage', $c);
        if ($stPackage) {
            return $stPackage->get('version_major') . '.' . $stPackage->get('version_minor') . '.' . $stPackage->get('version_patch') . '-' . $stPackage->get('release');
        }

        $gitpackagemanagement = $this->modx->getService('gitpackagemanagement', 'GitPackageManagement', $this->modx->getOption('gitpackagemanagement.core_path', null, $this->modx->getOption('core_path') . 'components/gitpackagemanagement/') . 'model/gitpackagemanagement/');
        if (!($gitpackagemanagement instanceof GitPackageManagement)) {
            return false;
        }

        /**
         * Check in git package management table
         */
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
}