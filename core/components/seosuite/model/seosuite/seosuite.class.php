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
    public $config = [];

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
            'seoTabNotice'  => $seoTabNotice,
        ], $options);

        /* Retrieve all plugin classes. */
        $this->setPlugins();

        $this->config = array_merge([
            'namespace'                 => 'seosuite',
            'lexicons'                  => ['seosuite:default', 'seosuite:tab_meta', 'seosuite:tab_seo', 'seosuite:tab_social'],
            'base_path'                 => $corePath,
            'core_path'                 => $corePath,
            'model_path'                => $corePath . 'model/',
            'processors_path'           => $corePath . 'processors/',
            'elements_path'             => $corePath . 'elements/',
            'chunks_path'               => $corePath . 'elements/chunks/',
            'plugins_path'              => $corePath . 'elements/plugins/',
            'snippets_path'             => $corePath . 'elements/snippets/',
            'templates_path'            => $corePath . 'templates/',
            'assets_path'               => $assetsPath,
            'js_url'                    => $assetsUrl . 'js/',
            'css_url'                   => $assetsUrl . 'css/',
            'assets_url'                => $assetsUrl,
            'connector_url'             => $assetsUrl . 'connector.php',
            'version'                   => '1.0.0',
            'branding_url'              => $this->modx->getOption('seosuite.branding_url', null, ''),
            'branding_help_url'         => $this->modx->getOption('seosuite.branding_url_help', null, ''),
            'exclude_words'             => array_filter(explode(',', $this->modx->getOption('seosuite.exclude_words', null, ''))),
            'default_redirect_type'     => $this->modx->getOption('seosuite.default_redirect_type', null, 'HTTP/1.1 301 Moved Permanently'),
            'tab_seo'                   => [
                'permission'                => (bool) $this->modx->hasPermission('seosuite_tab_seo'),
                'default_index_type'        => (int) $this->modx->getOption('seosuite.tab_seo_default_index_type', null, 1),
                'default_follow_type'       => (int) $this->modx->getOption('seosuite.tab_seo_default_follow_type', null, 1),
                'default_sitemap'           => (int) $this->modx->getOption('seosuite.tab_seo_default_sitemap', null, 0),
            ],
            'tab_social'                => [
                'permission'                => (bool) $this->modx->hasPermission('seosuite_tab_social'),
                'og_types'                  => explode(',', $this->modx->getOption('seosuite.tab_social_og_types')),
                'default_og_type'           => explode(',', $this->modx->getOption('seosuite.tab_social_og_types'))[0],
                'twitter_cards'             => explode(',', $this->modx->getOption('seosuite.tab_social_twitter_cards')),
                'default_twitter_card'      => explode(',', $this->modx->getOption('seosuite.tab_social_twitter_cards'))[0],
                'image_types'               => 'jpg,jpeg.png,gif'
            ],
            'meta'                      => [
                'preview.length_desktop_title'       => $this->modx->getOption('seosuite.meta.preview.length_desktop_title', null, null),
                'preview.length_desktop_description' => $this->modx->getOption('seosuite.meta.preview.length_desktop_description', null, null),
                'preview.length_mobile_title'        => $this->modx->getOption('seosuite.meta.preview.length_mobile_title', null, null),
                'preview.length_mobile_description'  => $this->modx->getOption('seosuite.meta.preview.length_mobile_description', null, null),
                'permission'                         => (bool) $this->modx->hasPermission('seosuite_meta'),
                'counter_fields'                     => $this->modx->getOption('seosuite.meta.counter_fields', null, 'longtitle:70,description:160,content'),
                'default_meta_description'           => $this->modx->getOption('seosuite.meta.default_meta_description', null, '[{"type":"placeholder","value":"description"}]'),
                'default_meta_title'                 => $this->modx->getOption('seosuite.meta.default_meta_title', null, '[{"type":"placeholder","value":"title"},{"type":"text","value":" | "},{"type":"placeholder","value":"site_name"}]'),
                'disabled_templates'                 => $this->modx->getOption('seosuite.meta.disabled_templates'),
                'max_keywords_description'           => (int) $this->modx->getOption('seosuite.meta.max_keywords_description', null, 8),
                'max_keywords_title'                 => (int) $this->modx->getOption('seosuite.meta.max_keywords_title', null, 4),
                'search_engine'                      => $this->modx->getOption('seosuite.meta.searchengine', null, 'google')
            ],
            'sitemap'                   => [
                'babel_add_alternate_links' => (bool) $this->modx->getOption('seosuite.sitemap.babel.add_alternate_links', null, true),
                'dependent_ultimateparent'  => (bool) $this->modx->getOption('seosuite.sitemap.dependent_ultimateparent', null, false),
                'default_changefreq'        => $this->modx->getOption('seosuite.sitemap.default_changefreq', null, 'weekly'),
                'default_priority'          => $this->modx->getOption('seosuite.sitemap.default_priority', null, '0.5')
            ]
        ], $options);

        $this->modx->addPackage('seosuite', $this->getOption('modelPath'));

        $query = $this->modx->newQuery('SeoSuiteResource');
        $query->where(['keywords' => 'test']);

        if (is_array($this->config['lexicons'])) {
            foreach ($this->config['lexicons'] as $lexicon) {
                $this->modx->lexicon->load($lexicon);
            }
        } else {
            $this->modx->lexicon->load($this->config['lexicons']);
        }
    }

    /**
     * @access public.
     * @return String|Boolean.
     */
    public function getHelpUrl()
    {
        if (!empty($this->config['branding_help_url'])) {
            return $this->config['branding_help_url'] . '?v=' . $this->config['version'];
        }

        return false;
    }

    /**
     * @access public.
     * @return String|Boolean.
     */
    public function getBrandingUrl()
    {
        if (!empty($this->config['branding_url'])) {
            return $this->config['branding_url'];
        }

        return false;
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
        $this->modx->log(xPDO::LOG_LEVEL_ERROR,'SeoSuite->findRedirectSuggestions deprecated, use SeoSuiteUrl->findRedirectSuggestions method.');

        return [];
    }

    /**
     * Split an url string into an array with separate words.
     *
     * @param   string $input
     * @return  array  An array with all the separate words
     */
    public function splitUrl($input)
    {
        $this->modx->log(xPDO::LOG_LEVEL_ERROR,'SeoSuite->splitUrl deprecated');

        return [];
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
        $this->modx->log(xPDO::LOG_LEVEL_ERROR,'SeoSuite->getExcludeWords deprecated');

        return [];
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
     * @deprecated This should be refactored
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
     * @deprecated This is not needed anymore.
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

    /**
     * @deprecated
     * @return bool|string
     */
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
                $chunk = $this->getTplChunk($name);
                if ($chunk === false) {
                    if (class_exists('pdoTools') && $pdo = $this->modx->getService('pdoTools')) {
                        return $pdo->getChunk($name, $properties);
                    } else {
                        return false;
                    }
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
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
     * @param string $postFix
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function getTplChunk($name, $postFix = '.chunk.tpl')
    {
        $chunk = false;
        $file = $this->options['chunksPath'] . strtolower($name) . $postFix;

        if (file_exists($file)) {
            $content = file_get_contents($file);
            $chunk   = $this->modx->newObject('modChunk');

            $chunk->set('name', $name);
            $chunk->setContent($content);
        }

        return $chunk;
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

    /**
     * @access public.
     * @return Array.
     */
    public function getResourceDefaultProperties()
    {
        return [
            'index_type'            => $this->config['tab_seo']['default_index_type'],
            'follow_type'           => $this->config['tab_seo']['default_follow_type'],
            'searchable'            => 1,
            'override_uri'          => 0,
            'uri'                   => '',
            'sitemap'               => $this->config['tab_seo']['default_sitemap'],
            'sitemap_prio'          => 'normal',
            'sitemap_changefreq'    => 'weekly',
            'canonical'             => 0,
            'canonical_uri'         => ''
        ];
    }

    /**
     * Get the resource properties of a resource.
     *
     * @access public.
     * @param Integer $id.
     * @return Array
     */
    public function getResourceProperties($id)
    {
        $defaultProperties = $this->getResourceDefaultProperties();

        $resource = $this->modx->getObject('modResource', [
            'id' => $id
        ]);

        if ($resource) {
            $defaultProperties['searchable']   = $resource->get('searchable') ? 1 : 0;
            $defaultProperties['override_uri'] = $resource->get('uri_override') ? 1 : 0;
            $defaultProperties['uri']          = $resource->get('uri');

            $object = $this->modx->getObject('SeoSuiteResource', [
                'resource_id' => $resource->get('id')
            ]);

            if ($object) {
                $properties = $object->toArray();

                unset($properties['id'], $properties['resource_id'], $properties['editedon']);

                return array_merge($defaultProperties, $properties);
            }
        }

        return $defaultProperties;
    }

    /**
     * Set the resource properties of a resource.
     *
     * @access public.
     * @param Integer $id.
     * @param Array $values.
     * @return Boolean.
     */
    public function setResourceProperties($id, array $values = [])
    {
        $resource = $this->modx->getObject('modResource', [
            'id' => $id
        ]);

        if ($resource) {
            $properties = array_merge($this->getResourceProperties($id), $values);

            $object = $this->modx->getObject('SeoSuiteResource', [
                'resource_id' => $resource->get('id')
            ]);

            if (!$object) {
                $object = $this->modx->newObject('SeoSuiteResource', [
                    'resource_id' => $resource->get('id')
                ]);
            }

            if ($object) {
                $object->fromArray(array_merge($object->toArray(), $properties));

                if ($object->save()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Removes the resource properties of a resource.
     *
     * @access public.
     * @param Integer $id.
     * @return Boolean.
     */
    public function removeResourceProperties($id)
    {
        $object = $this->modx->getObject('SeoSuiteResource', [
            'resource_id' => $id
        ]);

        if ($object) {
            if ($object->remove()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @access public.
     * @return Array.
     */
    public function getSocialDefaultProperties()
    {
        return [
            'og_title'             => '',
            'og_description'       => '',
            'og_image'             => '',
            'og_image_alt'         => '',
            'og_type'              => $this->config['tab_social']['default_og_type'],
            'twitter_title'        => '',
            'twitter_description'  => '',
            'twitter_image'        => '',
            'twitter_image_alt'    => '',
            'twitter_card'         => $this->config['tab_social']['default_twitter_card']
        ];
    }

    /**
     * Get the social properties of a resource.
     *
     * @access public.
     * @param Integer $id.
     * @return Array.
     */
    public function getSocialProperties($id)
    {
        $defaultProperties = $this->getSocialDefaultProperties();

        $resource = $this->modx->getObject('modResource', [
            'id' => $id
        ]);

        if ($resource) {
            $object = $this->modx->getObject('SeoSuiteSocial', [
                'resource_id' => $resource->get('id')
            ]);

            if ($object) {
                $properties = $object->toArray();

                unset($properties['id'], $properties['resource_id'], $properties['editedon']);

                return array_merge($defaultProperties, $properties);
            }
        }

        return $defaultProperties;
    }

    /**
     * Set the social properties of a resource.
     *
     * @access public.
     * @param Integer $id.
     * @param Array $values.
     * @return Boolean.
     */
    public function setSocialProperties($id, array $values = [])
    {
        $resource = $this->modx->getObject('modResource', [
            'id' => $id
        ]);

        if ($resource) {
            $properties = array_merge($this->getSocialProperties($id), $values);

            $object = $this->modx->getObject('SeoSuiteSocial', [
                'resource_id' => $resource->get('id')
            ]);

            if (!$object) {
                $object = $this->modx->newObject('SeoSuiteSocial', [
                    'resource_id' => $resource->get('id')
                ]);
            }

            if ($object) {
                $object->fromArray(array_merge($object->toArray(), $properties));

                if ($object->save()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Removes the social properties of a resource.
     *
     * @access public.
     * @param Integer $id.
     * @return Boolean.
     */
    public function removeSocialProperties($id)
    {
        $object = $this->modx->getObject('SeoSuiteSocial', [
            'resource_id' => $id
        ]);

        if ($object) {
            if ($object->remove()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @access public.
     * @param Object $resource.
     * @return Boolean.
     */
    public function setRedirectProperties($resource)
    {
        if ($resource) {
            $properties = $resource->getProperties('seosuite');

            if (isset($properties['uri'])) {
                $oldUrl = trim($properties['uri'], '/');
                $newUrl = trim($resource->get('uri'), '/');

                if ($oldUrl !== $newUrl && $oldUrl !== '' && $newUrl !== '') {
                    if ($this->handleRedirect($oldUrl, $newUrl)) {
                        $object = $this->modx->newObject('SeoSuiteRedirect');

                        if ($object) {
                            $object->fromArray([
                                'resource_id'   => $resource->get('id'),
                                'old_url'       => $oldUrl,
                                'new_url'       => $newUrl,
                                'redirect_type' => $this->config['default_redirect_type'],
                                'active'        => 1
                            ]);

                            $object->save();
                        }
                    }
                }
            }

            $resource->setProperties(array_merge($properties, [
                'uri' => trim($resource->get('uri'), '/')
            ]), 'seosuite');

            if ($resource->save()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @TODO check if a redirect exists or is infitite loop shizzle.
     *
     * @access protected.
     * @param String $oldUrl.
     * @param String $newUrl.
     * @return Boolean.
     */
    protected function handleRedirect($oldUrl, $newUrl)
    {
        return true;
    }

    /**
     * Get an array of words from the word txt files.
     * Uses words from https://github.com/digitalmethodsinitiative/dmi-tcat/tree/master/analysis/common/stopwords.
     * Also uses exclude words from system setting 'seosuite.exclude_words'.
     *
     * @access public.
     * @return Array.
     */
    public function getExcludeWords()
    {
        $words = [];
        $wordsPath = rtrim($this->config['elements_path'], '/') . '/stopwords/';

        if (is_dir($wordsPath)) {
            foreach (glob($wordsPath . '/*.txt') as $file) {
                $content = file_get_contents($file);

                if ($content) {
                    $words = array_merge($words, explode(PHP_EOL, $content));
                }
            }
        }

        return array_unique(array_filter(array_merge($words, $this->config['exclude_words'])));
    }

    /**
     * Renders the meta value from the configuration json to the output string.
     *
     * @param $json
     * @param $resourceArray
     * @return string
     */
    public function renderMetaValue($json, $resourceArray)
    {
        $output = [];

        if (!empty($json)) {
            $array = json_decode($json, true);

            if (is_array($array) && count($array) > 0) {
                foreach ($array as $item) {
                    if ($item['type'] === 'text') {
                        $output[] = $item['value'];
                    } else {
                        switch ($item['value']) {
                            case 'site_name':
                                $output[] = $this->modx->getOption($item['value']);
                                break;
                            case 'pagetitle':
                            case 'longtitle':
                            case 'description':
                            case 'introtext':
                                if (isset($resourceArray[$item['value']])) {
                                    $output[] = $resourceArray[$item['value']];
                                }
                                break;
                            case 'title':
                                $output[] = $resourceArray['longtitle'] ?: $resourceArray['pagetitle'];
                                break;
                        }
                    }
                }
            }
        }

        return implode('', $output);
    }
}
