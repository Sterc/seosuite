<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuite
{
    /**
     * @access public.
     * @var modX.
     */
    public $modx;

    /**
     * @access public.
     * @var Array.
     */
    public $config = [];

    /**
     * Holds all plugins.
     *
     * @var array $plugins
     */
    protected $plugins = [];

    /**
     * @access public.
     * @param modX $modx.
     * @param Array $config.
     */
    public function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;

        $corePath   = $this->modx->getOption('seosuite.core_path', $config, $this->modx->getOption('core_path') . 'components/seosuite/');
        $assetsUrl  = $this->modx->getOption('seosuite.assets_url', $config, $this->modx->getOption('assets_url') . 'components/seosuite/');
        $assetsPath = $this->modx->getOption('seosuite.assets_path', $config, $this->modx->getOption('assets_path') . 'components/seosuite/');

        $this->config = array_merge([
            'namespace'                  => 'seosuite',
            'lexicons'                   => ['seosuite:mgr', 'seosuite:default', 'seosuite:tab_meta', 'seosuite:tab_seo', 'seosuite:tab_social'],
            'base_path'                  => $corePath,
            'core_path'                  => $corePath,
            'model_path'                 => $corePath . 'model/',
            'processors_path'            => $corePath . 'processors/',
            'elements_path'              => $corePath . 'elements/',
            'chunks_path'                => $corePath . 'elements/chunks/',
            'plugins_path'               => $corePath . 'elements/plugins/',
            'snippets_path'              => $corePath . 'elements/snippets/',
            'templates_path'             => $corePath . 'templates/',
            'assets_path'                => $assetsPath,
            'js_url'                     => $assetsUrl . 'js/',
            'css_url'                    => $assetsUrl . 'css/',
            'assets_url'                 => $assetsUrl,
            'connector_url'              => $assetsUrl . 'connector.php',
            'version'                    => '2.0.0',
            'branding_url'               => $this->modx->getOption('seosuite.branding_url', null, ''),
            'branding_help_url'          => $this->modx->getOption('seosuite.branding_url_help', null, ''),
            'blocked_words'              => array_filter(explode(',', $this->modx->getOption('seosuite.blocked_words', null, ''))),
            'exclude_words'              => array_filter(explode(',', $this->modx->getOption('seosuite.exclude_words', null, ''))),
            'disabled_templates'         => array_filter(explode(',', $this->modx->getOption('seosuite.disabled_templates'))),
            'default_redirect_type'      => $this->modx->getOption('seosuite.default_redirect_type', null, 'HTTP/1.1 301 Moved Permanently'),
            'placeholder_plugin_enabled' => (bool) $this->modx->getOption('seosuite.placeholder_plugin_enabled', null, true),
            'tab_seo'                    => [
                'permission'                => (bool) $this->modx->hasPermission('seosuite_tab_seo'),
                'default_index_type'        => (bool) $this->modx->getOption('seosuite.tab_seo_default_index_type', null, 1),
                'default_follow_type'       => (bool) $this->modx->getOption('seosuite.tab_seo_default_follow_type', null, 1),
                'default_sitemap'           => (bool) $this->modx->getOption('seosuite.tab_seo_default_sitemap', null, 1),
            ],
            'tab_social'                 => [
                'permission'                => (bool) $this->modx->hasPermission('seosuite_tab_social'),
                'og_types'                  => explode(',', $this->modx->getOption('seosuite.tab_social.og_types', null, 'website')),
                'default_og_type'           => explode(',', $this->modx->getOption('seosuite.tab_social.og_types', null, 'website'))[0],
                'twitter_cards'             => explode(',', $this->modx->getOption('seosuite.tab_social.twitter_cards', null, 'summary,summary_large_image,app,player')),
                'default_twitter_card'      => explode(',', $this->modx->getOption('seosuite.tab_social.twitter_cards', null, 'summary,summary_large_image,app,player'))[0],
                'twitter_creator_id'        => $this->modx->getOption('seosuite.tab_social.twitter_creator_id'),
                'default_og_image'          => $this->modx->getOption('seosuite.tab_social.default_og_image'),
                'default_twitter_image'     => $this->modx->getOption('seosuite.tab_social.default_twitter_image'),
                'default_inherit_facebook'  => true,
                'image_types'               => 'jpg,jpeg,png,gif'
            ],
            'meta'                       => [
                'permission'                => (bool) $this->modx->hasPermission('seosuite_tab_meta'),
                'field_counters'            => $this->getFieldCounters($this->modx->getOption('seosuite.meta.field_counters', null, 'longtitle:30|70,description:70|155')),
                'keywords_field_counters'   => $this->getKeywordsFieldCounters($this->modx->getOption('seosuite.meta.keywords_field_counters', null, 'longtitle:4,description:8,content')),
                'default_meta_title'        => $this->modx->getOption('seosuite.meta.default_meta_title', null, '[[+longtitle]] | [[++site_name]]'),
                'default_meta_description'  => $this->modx->getOption('seosuite.meta.default_meta_description', null, '[[+description]]'),
                'default_alternate_context' => $this->modx->getOption('seosuite.meta.default_alternate_context', null, ''),
                'preview'                   => [
                    'mode'                      => $this->modx->getOption('seosuite.meta.searchmode', null, 'mobile'),
                    'engine'                    => $this->modx->getOption('seosuite.meta.searchengine', null, 'google'),
                    'desktop'                   => [
                        'title'                     => (int) $this->modx->getOption('seosuite.meta.preview.length_desktop_title', null, 70),
                        'description'               => (int) $this->modx->getOption('seosuite.meta.preview.length_desktop_description', null, 160),
                    ],
                    'mobile'                    => [
                        'title'                     => (int) $this->modx->getOption('seosuite.meta.preview.length_mobile_title', null, 78),
                        'description'               => (int) $this->modx->getOption('seosuite.meta.preview.length_mobile_description', null, 130)
                    ]
                ]
            ],
            'sitemap'                   => [
                'babel_add_alternate_links' => (bool) $this->modx->getOption('seosuite.sitemap.babel.add_alternate_links', null, true),
                'dependent_ultimateparent'  => (bool) $this->modx->getOption('seosuite.sitemap.dependent_ultimateparent', null, false),
                'default_changefreq'        => $this->modx->getOption('seosuite.sitemap.default_changefreq', null, 'weekly'),
                'default_priority'          => $this->modx->getOption('seosuite.sitemap.default_priority', null, '0.5')
            ]
        ], $config);

        $this->modx->addPackage('seosuite', $this->config['model_path']);

        if (is_array($this->config['lexicons'])) {
            foreach ($this->config['lexicons'] as $lexicon) {
                $this->modx->lexicon->load($lexicon);
            }
        } else {
            $this->modx->lexicon->load($this->config['lexicons']);
        }

        /* Retrieve all plugin classes. */
        $this->setPlugins();
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
     * @access public.
     * @param String $key.
     * @param Array $options.
     * @param Mixed $default.
     * @return Mixed.
     */
    public function getOption($key, array $options = [], $default = null)
    {
        if (isset($options[$key])) {
            return $options[$key];
        }

        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return $this->modx->getOption($this->config['namespace'] . '.' . $key, $options, $default);
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
        $file = $this->config['chunks_path'] . strtolower($name) . $postFix;

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
     * @param String $fields.
     * @return Array.
     */
    public function getFieldCounters($fields)
    {
        $output = [];

        foreach (explode(',', trim($fields)) as $field) {
            list($name, $count) = explode(':', $field);

            $min = 0;
            $max = $count;

            if (strpos($count, '|')) {
                list($min, $max) = explode('|', $count);
            }

            $output[$name] = [
                'min'   => (int) $min,
                'max'   => (int) $max ?: $min
            ];
        }

        return $output;
    }

    /**
     * @access public.
     * @param String $fields.
     * @return Array.
     */
    public function getKeywordsFieldCounters($fields)
    {
        $output = [];

        foreach (explode(',', trim($fields)) as $field) {
            $field = trim($field);

            if (strpos($field, ':')) {
                list($field, $max) = explode(':', $field);

                $output[$field] = (int) $max;
            } else {
                $output[$field] = 0;
            }
        }

        return $output;
    }

    /**
     * @access public.
     * @return Array.
     */
    public function getResourceDefaultProperties()
    {
        return [
            'keywords'              => '',
            'use_default_meta'      => 1,
            'meta_title'            => [],
            'meta_description'      => [],
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
            'og_title'              => '',
            'og_description'        => '',
            'og_image'              => '',
            'og_image_alt'          => '',
            'og_type'               => $this->config['tab_social']['default_og_type'],
            'twitter_title'         => '',
            'twitter_description'   => '',
            'twitter_image'         => '',
            'twitter_image_alt'     => '',
            'twitter_creator_id'    => '',
            'twitter_card'          => $this->config['tab_social']['default_twitter_card'],
            'inherit_facebook'      => $this->config['tab_social']['default_inherit_facebook']
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
     * Renders the meta value.
     *
     * @access public.
     * @param String $value.
     * @param Array $fields.
     * @param String|Array $skip.
     * @return Array.
     */
    public function renderMetaValue($value, array $fields = [], $skip = null)
    {
        $processedValue     = $value;
        $unProcessedValue   = $value;

        if (!empty($value)) {
            $data = array_map(function ($value) {
                return is_string($value) ? trim($value) : $value;
            }, $fields);

            if (empty($data['longtitle'])) {
                $data['longtitle'] = $data['pagetitle'];
            }

            $parser = $this->modx->newObject('modChunk', [
                'name' => $this->config['namespace'] . uniqid()
            ]);

            if ($parser) {
                $parser->setCacheable(false);

                $processedValue = $parser->process($data, $processedValue);

                if (!empty($skip)) {
                    foreach ((array) $skip as $key) {
                        $data[$key] = '';
                    }
                }

                $unProcessedValue = $parser->process($data, $unProcessedValue);
            }
        }

        return [
            'processed'     => htmlentities($processedValue),
            'unprocessed'   => htmlentities($unProcessedValue)
        ];
    }

    /**
     * This strips the domain from the request.
     * For example: domain.tld/path/to/page will become path/to/page.
     *
     * @access public.
     * @param String $request.
     * @return String.
     */
    public function formatUrl($request)
    {
        if (!empty($request)) {
            $parts   = parse_url($request);

            if (isset($parts['path'])) {
                $request = $parts['path'];
            }
        }

        return urldecode(trim($request, '/'));
    }
}
