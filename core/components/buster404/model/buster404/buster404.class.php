<?php

/**
 * The main Buster404 service class.
 *
 * @package buster404
 */
class Buster404
{
    public $modx = null;
    public $namespace = 'buster404';
    public $cache = null;
    public $options = array();

    public function __construct(modX &$modx, array $options = array())
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, 'buster404');

        $corePath = $this->getOption(
            'core_path',
            $options,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/buster404/'
        );
        $assetsPath = $this->getOption(
            'assets_path',
            $options,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/buster404/'
        );
        $assetsUrl = $this->getOption(
            'assets_url',
            $options,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/buster404/'
        );

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
            'connectorUrl' => $assetsUrl . 'connector.php'
        ), $options);

        $this->modx->addPackage('buster404', $this->getOption('modelPath'));
        $this->modx->lexicon->load('buster404:default');
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
     * Makes use of common stopwords libraries from https://github.com/digitalmethodsinitiative/dmi-tcat/tree/master/analysis/common/stopwords
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
            // Try to find a resource with an exact matching alias
            // or a resource with matching pagetitle, where non-alphanumer chars are replaced with space
            $q = $this->modx->newQuery('modResource');
            $q->where(array(
                  array(
                      'alias:=' => $searchString,
                      'OR:pagetitle:=' => preg_replace('/[^A-Za-z0-9 ]/', ' ', $searchString)
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
        return $output;
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
        $this->checkSeoTab();
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
            /* SeoTab is not installed */
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('buster404.seotab.notfound'));
            return false;
        }

        $c = $this->modx->newQuery('transport.modTransportPackage');
        $c->where(array(
            'workspace' => 1,
            "(SELECT
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
        ));
        $c->where(array(
            'modTransportPackage.package_name' => 'SEO Tab',
            'installed:IS NOT' => null
        ));
        $stPackage = $this->modx->getObject('transport.modTransportPackage', $c);
        if ($stPackage) {
            $version_major = (int) $stPackage->get('version_major');
            if ($version_major < 2) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('buster404.seotab.versioninvalid'));
                return false;
            }
        }
        return true;
    }
}
