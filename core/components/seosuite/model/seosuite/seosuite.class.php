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
     * @param array $contextSiteUrls An array with site_url => context combinations. If not empty, limits to context
     * @return array An array with modResource objects
     */
    public function findRedirectSuggestions($url, $contextSiteUrls = array())
    {
        $output = [];
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
                        $q->where(array(
                            'context_key' => $context
                        ));
                    }
                    $q->where(array(
                        array(
                            'alias:LIKE' => '%' . $word . '%',
                            'OR:pagetitle:LIKE' => '%' . $word . '%'
                        ),
                        array(
                            'AND:published:=' => true,
                            'AND:deleted:=' => false
                        )
                    ));
                    $excludeWords = $this->getExcludeWords();
                    if (is_array($excludeWords) || is_object($excludeWords)) {
                        foreach ($excludeWords as $excludeWord) {
                            $q->where(array(
                                'alias:NOT LIKE' => '%' . $excludeWord . '%',
                                'pagetitle:NOT LIKE' => '%' . $excludeWord . '%',
                            ));
                        }
                    }
                    $q->prepare();

                    $results = $this->modx->query($q->toSql());
                    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                        $output[] = $row['modResource_id'];
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
        $output = array();
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
        $filtered = array();
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
    public function getLangs() {
        $entries = $this->modx->lexicon->loadCache('seosuite');
        $langs = 'Ext.applyIf(MODx.lang,' . $this->modx->toJSON($entries) . ');';
        return $langs;
    }

    /**
     * Returns a list of all context site urls (if any)
     * @return array
     */
    public function getSiteUrls()
    {
        $urls = array();
        $q = $this->modx->newQuery('modContextSetting');
        $q->where(array(
            'key' => 'site_url',
            'context_key:!=' => 'mgr'
        ));
        $collection = $this->modx->getCollection('modContextSetting', $q);
        foreach ($collection as $item) {
            $urls[$item->get('value')] = $item->get('context_key');
        }
        return $urls;
    }

    public function f() {
        // Only run if we're in the manager
        if (!$this->modx->context || $this->modx->context->get('key') !== 'mgr') {
            return;
        }

        $c = $this->modx->newQuery('transport.modTransportPackage', array('package_name' => __CLASS__));
        $c->innerJoin('transport.modTransportProvider', 'modTransportProvider', 'modTransportProvider.id = modTransportPackage.provider');
        $c->select('modTransportProvider.service_url');
        $c->sortby('modTransportPackage.created', 'desc');
        $c->limit(1);
        if ($c->prepare() && $c->stmt->execute()) {
            $url = $c->stmt->fetchColumn();
            if (stripos($url, 'modstore')) {
                $this->ms();
                return;
            }
        }

        $this->mm();
    }

    protected function ms() {
        $result = true;
        $key = strtolower(__CLASS__);
        /** @var modDbRegister $registry */
        $registry = $this->modx->getService('registry', 'registry.modRegistry')
            ->getRegister('user', 'registry.modDbRegister');
        $registry->connect();
        $registry->subscribe('/modstore/' . md5($key));
        if ($res = $registry->read(array('poll_limit' => 1, 'remove_read' => false))) {
            return $res[0];
        }
        $c = $this->modx->newQuery('transport.modTransportProvider', array('service_url:LIKE' => '%modstore%'));
        $c->select('username,api_key');
        /** @var modRest $rest */
        $rest = $this->modx->getService('modRest', 'rest.modRest', '', array(
            'baseUrl' => 'https://modstore.pro/extras',
            'suppressSuffix' => true,
            'timeout' => 1,
            'connectTimeout' => 1,
            'format' => 'xml',
        ));

        if ($rest) {
            $level = $this->modx->getLogLevel();
            $this->modx->setLogLevel(modX::LOG_LEVEL_FATAL);
            /** @var RestClientResponse $response */
            $response = $rest->get('stat', array(
                'package' => $key,
                'host' => @$_SERVER['HTTP_HOST'],
                'keys' => $c->prepare() && $c->stmt->execute()
                    ? $c->stmt->fetchAll(PDO::FETCH_ASSOC)
                    : array(),
            ));
            $result = $response->process() == 'true';
            $this->modx->setLogLevel($level);
        }
        $registry->subscribe('/modstore/');
        $registry->send('/modstore/', array(md5($key) => $result), array('ttl' => 3600 * 24));

        return $result;
    }

    protected function mm() {
        // Get the public key from the .pubkey file contained in the package directory
        $pubKeyFile = $this->options['corePath'] . '.pubkey';
        $key = file_exists($pubKeyFile) ? file_get_contents($pubKeyFile) : '';
        $domain = $this->modx->getOption('http_host');
        if (strpos($key, '@@') !== false) {
            $pos = strpos($key, '@@');
            $domain = substr($key, 0, $pos);
            $key = substr($key, $pos + 2);
        }
        $check = false;
        // No key? That's a really good reason to check :)
        if (empty($key)) {
            $check = true;
        }
        // Doesn't the domain in the key file match the current host? Then we should get that sorted out.
        if ($domain !== $this->modx->getOption('http_host')) {
            $check = true;
        }
        // the .pubkey_c file contains a unix timestamp saying when the pubkey was last checked
        $modified = file_exists($pubKeyFile . '_c') ? file_get_contents($pubKeyFile . '_c') : false;
        if (!$modified ||
            $modified < (time() - (60 * 60 * 24 * 7)) ||
            $modified > time()) {
            $check = true;
        }

        if ($check) {
            $provider = false;
            $c = $this->modx->newQuery('transport.modTransportPackage');
            $c->where(array(
                'signature:LIKE' => 'formalicious-%',
            ));
            $c->sortby('installed', 'DESC');
            $c->limit(1);
            $package = $this->modx->getObject('transport.modTransportPackage', $c);
            if ($package instanceof modTransportPackage) {
                $provider = $package->getOne('Provider');
            }
            if (!$provider) {
                $provider = $this->modx->getObject('transport.modTransportProvider', array(
                    'service_url' => 'https://rest.modmore.com/'
                ));
            }
            if ($provider instanceof modTransportProvider) {
                $this->modx->setOption('contentType', 'default');
                // The params that get sent to the provider for verification
                $params = array(
                    'key' => $key,
                    'package' => strtolower(__CLASS__),
                );
                // Fire it off and see what it gets back from the XML..
                $response = $provider->request('license', 'GET', $params);
                $xml = $response->toXml();
                $valid = (int)$xml->valid;
                // If the key is found to be valid, set the status to true
                if ($valid) {
                    // It's possible we've been given a new public key (typically for dev licenses or when user has unlimited)
                    // which we will want to update in the pubkey file.
                    $updatePublicKey = (bool)$xml->update_pubkey;
                    if ($updatePublicKey > 0) {
                        file_put_contents($pubKeyFile,
                            $this->modx->getOption('http_host') . '@@' . (string)$xml->pubkey);
                    }
                    file_put_contents($pubKeyFile . '_c', time());
                    return;
                }

                // If the key is not valid, we have some more work to do.
                $message = (string)$xml->message;
                $age = (int)$xml->case_age;
                $url = (string)$xml->case_url;
                $warning = false;
                if ($age >= 7) {
                    $warning = <<<HTML
    var warning = '<div style="width: 100%;border: 1px solid #dd0000;background-color: #F9E3E3 !important;padding: 1em;margin-top: 1em; font-weight: bold; line-height:20px; box-sizing: border-box;">';
    warning += '<a href="$url" style="float:right; margin-left: 1em;" target="_blank" class="x-btn x-btn-small x-btn-icon-small-left primary-button">Fix the license</a>The SEO Suite license on this site is invalid. Please click the button on the right to correct the problem. Error: {$message}';
    warning += '</div>';
HTML;
                } elseif ($age >= 2) {
                    $warning = <<<HTML
    var warning = '<div style="width: 100%;border: 1px solid #dd0000;background-color: #F9E3E3 !important;padding: 1em;margin-top: 1em; line-height:20px; box-sizing: border-box;">';
    warning += '<a href="$url" style="float:right; margin-left: 1em;" target="_blank" class="x-btn x-btn-small x-btn-icon-small-left primary-button">Fix the license</a>Oops, there is an issue with the SEOSuite license. Perhaps your site recently moved to a new domain? Please click the button on the right or contact your development team to correct the problem.';
    warning += '</div>';
HTML;
                }
                if ($warning) {
                    $output = <<<HTML
    <script type="text/javascript">
    {$warning}
    function showSeoSuiteWarning() {
        setTimeout(function() {
            var scAdded = false,
                homePanel = Ext.getCmp('seosuite-panel-home');
            if (homePanel) {
                homePanel.insert(1,{xtype: 'panel', html: warning, bodyStyle: 'margin-bottom: 1em'});
                homePanel.doLayout();
                scAdded = true;
            }
            
            if (!scAdded) {
                setTimeout(showSeoSuiteWarning, 300);
            }
        }, 300);
    }
    showSeoSuiteWarning();
    </script>
HTML;
                    if ($this->modx->controller instanceof modManagerController) {
                        $this->modx->controller->addHtml($output);
                    } else {
                        $this->modx->regClientHTMLBlock($output);
                    }
                }

            }
            else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'UNABLE TO VERIFY MODMORE LICENSE - PROVIDER NOT FOUND!');
            }
        }
    }
}