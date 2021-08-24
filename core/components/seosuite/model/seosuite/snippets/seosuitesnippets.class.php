<?php
require_once dirname(__DIR__) . '/seosuite.class.php';

/**
 * Class SeoSuiteSnippets.
 */
class SeoSuiteSnippets extends SeoSuite
{
    const PHS_PREFIX = 'ss_meta';

    /**
     * Snippet for outputting meta data.
     * @param $properties
     * @return string
     */
    public function seosuiteMeta($properties)
    {
        $id                  = $this->modx->getOption('id', $properties, $this->modx->resource->get('id'));
        $tpl                 = $this->modx->getOption('tpl', $properties, 'tplMeta');
        $tplSocial           = $this->modx->getOption('tplSocial', $properties, 'tplMetaSocial');
        $tplTitle            = $this->modx->getOption('tplTitle', $properties, 'tplMetaTitle');
        $tplLink             = $this->modx->getOption('tplLink', $properties, 'tplLink');
        $tplAlternateWrapper = $this->modx->getOption('tplAlternateWrapper', $properties, 'tplAlternateWrapper');
        $toPlaceholders      = $this->modx->getOption('toPlaceholders', $properties, false);

        $meta = [
            '_meta_title'       => [
                'name'              => 'title',
                'value'             => $this->config['meta']['default_meta_title'],
                'tpl'               => $tplTitle
            ],
            '_meta_description' => [
                'name'              => 'description',
                'value'             => $this->config['meta']['default_meta_description'],
                'tpl'               => $tpl
            ]
        ];

        $ssResource = $this->modx->getObject('SeoSuiteResource', [
            'resource_id' => $id
        ]);

        $canonicalUrl = $this->modx->makeUrl($id, null, null, 'full');
        if ($ssResource) {
            $meta['_robots'] = [
                'name'  => 'robots',
                'value' => implode(',', [
                    $ssResource->get('index_type') ? 'index' : 'noindex',
                    $ssResource->get('follow_type') ? 'follow' : 'nofollow'
                ]),
                'tpl'   => $tpl
            ];

            $meta['_keywords'] = [
                'name'  => 'keywords',
                'value' => $ssResource->get('keywords'),
                'tpl'   => $tpl
            ];

            if ($ssResource->get('canonical') && !empty($ssResource->get('canonical_uri'))) {
                $canonicalUrl = rtrim($this->modx->makeUrl($this->modx->getOption('site_start'), null, null, 'full'), '/') . '/' . ltrim($ssResource->get('canonical_uri'), '/');
            }
        } else {
            $meta['_robots'] = [
                'name'  => 'robots',
                'value' => implode(',', [
                    $this->config['tab_seo']['default_index_type'] ? 'index' : 'noindex',
                    $this->config['tab_seo']['default_follow_type'] ? 'follow' : 'nofollow'
                ]),
                'tpl'   => $tpl
            ];
        }

        $meta['_canonical'] = [
            'name'  => 'canonical',
            'value' => $canonicalUrl,
            'tpl'   => $tpl
        ];

        if (!empty($this->config['tab_social']['default_og_image'])) {
            $meta['og_image'] = [
                'name'  => 'og:image',
                'value' => $this->config['tab_social']['default_og_image'],
                'tpl'   => $tplSocial
            ];
        }

        if (!empty($this->config['tab_social']['default_twitter_image'])) {
            $meta['twitter_image'] = [
                'name'  => 'twitter:image',
                'value' => $this->config['tab_social']['default_twitter_image'],
                'tpl'   => $tplSocial
            ];
        }

        if (!empty($this->config['tab_social']['twitter_creator_id'])) {
            $meta['twitter_creator_id'] = [
                'name'  => 'twitter:creator:id',
                'value' => $this->config['tab_social']['twitter_creator_id'],
                'tpl'   => $tplSocial
            ];
        }

        $ssSocial = $this->modx->getObject('SeoSuiteSocial', [
            'resource_id' => $id
        ]);

        if ($ssSocial) {
            foreach ((array) $ssSocial->getValues() as $key => $value) {
                if (in_array($key, ['id', 'resource_id', 'inherit_facebook', 'editedon'], true)) {
                    continue;
                }

                if (!empty($value)) {
                    $meta[$key] = [
                        'name'  => str_replace('_', ':', $key),
                        'tpl'   => $tplSocial,
                        'value' => $value
                    ];
                }
            }
        }

        $resourceArray = ($modResource = $this->modx->getObject('modResource', $id)) ? $modResource->toArray() : [];

        if ($alternatives = $this->getAlternateLinks($modResource)) {
            $values = [];
            $alternateHTML = '';

            foreach ($alternatives as $alternative) {
                $values[] = $this->getChunk($tplLink, [
                    'name'     => 'alternate',
                    'value'    => $alternative['url'],
                    'hreflang' => str_replace('_', '-', $alternative['locale'])
                ]);
            }

            $meta['_alternates'] = [
                'name'  => 'alternates',
                'value' => $this->modx->getChunk($tplAlternateWrapper, [
                    'output' => implode($values, PHP_EOL)
                ])
            ];
        }

        ksort($meta);

        $html = [];

        foreach ($meta as $key => $item) {
            $tpl = $item['tpl'] ?: null;
            $key = trim($key, '_');

            /* Unset tpl from placeholders. */
            unset($item['tpl']);

            if (in_array($key, ['meta_title', 'meta_description'], true)) {
                $item['value'] = $this->renderMetaValue($item['value'], $resourceArray)['processed'];
            } else if (in_array($key, ['og_image', 'twitter_image'], true)) {
                $ms_default_id = $this->modx->getOption('seosuite.default_media_source', null, $this->modx->getOption('default_media_source', null, 1));
                $ms_default = $this->modx->getObject('modMediaSource', $ms_default_id);
                $ms_base_url = $ms_default->get('properties')['baseUrl']['value'];
                $imageUrl = trim($ms_base_url, '/') . '/' . trim($item['value'], '/');
                $item['value'] = rtrim($this->modx->makeUrl($this->modx->getOption('site_start'), null, null, 'full'), '/') . '/' . trim($imageUrl, '/');
            }

            $html[$key] = $tpl ? $this->getChunk($tpl, $item) : $item['value'];
        }

        if ($toPlaceholders) {
            $this->modx->toPlaceholders($html, rtrim(self::PHS_PREFIX,'.'));
            $this->modx->toPlaceholder(rtrim(self::PHS_PREFIX,'.'), implode(PHP_EOL, $html));

            return '';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param array $scriptProperties
     * @return string
     */
    public function seosuiteSitemap(array $scriptProperties = [])
    {
        $allowSymlinks  = $this->modx->getOption('allowSymlinks', $scriptProperties, 0);
        $contexts       = $this->modx->getOption('contexts', $scriptProperties, null);
        $type           = $this->modx->getOption('type', $scriptProperties, '');
        $templates      = $this->modx->getOption('templates', $scriptProperties, '');
        $outerTpl       = $this->modx->getOption('outerTpl', $scriptProperties, 'sitemap/outertpl');
        $rowTpl         = $this->modx->getOption('rowTpl', $scriptProperties, 'sitemap/rowtpl');
        $alternateTpl   = $this->modx->getOption('alternateTpl', $scriptProperties, 'sitemap/alternatetpl');
        $indexOuterTpl  = $this->modx->getOption('indexOuterTpl', $scriptProperties, 'sitemap/index/outertpl');
        $indexRowTpl    = $this->modx->getOption('indexRowTpl', $scriptProperties, 'sitemap/index/rowtpl');
        $imagesOuterTpl = $this->modx->getOption('imageOuterTpl', $scriptProperties, 'sitemap/images/outertpl');
        $imagesRowTpl   = $this->modx->getOption('imagesRowTpl', $scriptProperties, 'sitemap/images/rowtpl');
        $imageTpl       = $this->modx->getOption('imageTpl', $scriptProperties, 'sitemap/images/imagetpl');

        /* Properly set contexts variable. */
        $contexts = $contexts ? explode(',', str_replace(' ', '', $contexts)) : [$this->modx->resource->get('context_key')];

        return $this->sitemap(
            $contexts,
            $allowSymlinks,
            [
                'outerTpl'       => $outerTpl,
                'rowTpl'         => $rowTpl,
                'alternateTpl'   => $alternateTpl,
                'type'           => $type,
                'indexOuterTpl'  => $indexOuterTpl,
                'indexRowTpl'    => $indexRowTpl,
                'imagesOuterTpl' => $imagesOuterTpl,
                'imagesRowTpl'   => $imagesRowTpl,
                'imageTpl'       => $imageTpl,
                'templates'      => $templates
            ]
        );
    }

    /**
     * Generate XML sitemap.
     *
     * @param array  $contextKey
     * @param string $allowSymlinks
     * @param array  $options
     *
     * @return string
     * @internal param string $type
     * @internal param string $templates
     *
     */
    protected function sitemap(array $contextKey = ['web'], $allowSymlinks = '', $options = [])
    {
        $outerTpl     = $options['outerTpl'];
        $rowTpl       = $options['rowTpl'];
        $query        = $this->buildQuery($contextKey, $allowSymlinks, $options);

        $resources = [];
        foreach ($this->modx->getIterator('modResource', $query) as $modResource) {
            $resources[$modResource->get('id')] = $modResource;
        }

        if ($options['type'] === 'index') {
            $outerTpl = $options['indexOuterTpl'];
            $rowTpl   = $options['indexRowTpl'];
        }

        if ($options['type'] === 'images') {
            return $this->sitemapImages($contextKey, $resources, $options);
        }

        /* If resources should be displayed based upon parent/ultimate parent properties. */
        $sitemapDependsOnUltimateParent = $this->config['sitemap']['dependent_ultimateparent'];
        if ($sitemapDependsOnUltimateParent) {
            $resources = $this->filterResourcesByParentProperties($resources);
        }

        $output = [];
        foreach ($resources as $resource) {
            $output[] = $this->getChunk(
                $rowTpl,
                array_merge(
                    $resource->toArray(),
                    [
                        'url'        => $this->modx->makeUrl($resource->get('id'), '', '', 'full'),
                        'alternates' => $this->getAlternateLinks($resource, $options),
                        'lastmod'    => date('c', $this->getLastModTime($options['type'], $resource)),
                        'changefreq' => !empty($resource->get('SeoSuiteResource.sitemap_changefreq')) ? $resource->get('SeoSuiteResource.sitemap_changefreq') : $this->config['sitemap']['default_changefreq'],
                        'priority'   => !empty($resource->get('SeoSuiteResource.sitemap_prio')) ? $resource->get('SeoSuiteResource.sitemap_prio') : $this->config['sitemap']['default_priority'],
                    ]
                )
            );
        }

        return $this->getChunk($outerTpl, ['wrapper' => implode('', $output)]);
    }

    /**
     * Get last modification time for a sitemap type of a specific resource.
     *
     * @param $type
     * @param $resource
     *
     * @return int
     */
    protected function getLastModTime($type, $resource)
    {
        $lastmod = 0;
        if ($type === 'index') {
            $content = $resource->get('content');
            preg_match_all('/\[\[[^[]*]]/', $content, $matches);

            if (count($matches) > 0) {
                foreach ($matches as $match) {
                    $match = trim($match[0], '[]!');
                    if (0 === strpos($match, 'StercSeoSiteMap')) {
                        /* Get snippet parameter values. */
                        preg_match('/&type=`(.*)`/', $match, $type);
                        preg_match('/&templates=`(.*)`/', $match, $templates);
                        preg_match('/&allowSymlinks=`(.*)`/', $match, $allowSymlinks);
                        preg_match('/&contexts=`(.*)`/', $match, $contexts);

                        $type          = (isset($type[1])) ? $type[1] : '';
                        $allowSymlinks = (isset($allowSymlinks[1])) ? $allowSymlinks[1] : 0;
                        $contexts      = (isset($contexts[1])) ? explode(',',str_replace(' ', '', $contexts[1])) : array($this->modx->resource->get('context_key'));
                        $templates     = (isset($templates[1])) ? $templates[1] : '';

                        /* If the sitemap type is images, set the last mod time to current time. */
                        if ($type === 'images') {
                            $lastmod = time();
                            continue;
                        }

                        $query     = $this->buildQuery($contexts, $allowSymlinks, ['type' => $type, 'templates' => $templates]);
                        $resources = $this->modx->getIterator('modResource', $query);
                        if ($resources) {
                            foreach ($resources as $resource) {
                                $createdon       = $resource->get('createdon');
                                $editedon        = $resource->get('editedon');
                                $resourceLastmod = strtotime((($editedon > 0) ? $editedon : $createdon));

                                if ($resourceLastmod > $lastmod) {
                                    $lastmod = $resourceLastmod;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $editedon  = $resource->get('editedon');
            $createdon = $resource->get('createdon');
            $lastmod   = strtotime((($editedon > 0) ? $editedon : $createdon));
        }

        return $lastmod;
    }

    /**
     * Adds alternative language links to sitemap XML.
     *
     * @param $resource
     * @param $options
     * @return array|string
     */
    protected function getAlternateLinks($resource, $options = [])
    {
        if (!$this->shouldAddBabelAlternativeLinks()) {
            return '';
        }

        $alternates   = [];
        $html         = [];
        $translations = $this->getBabel()->getLinkedResources($resource->get('id'));
        foreach ($translations as $contextKey => $resourceId) {
            $ctx = $this->modx->getContext($contextKey);

            $alternate = [
                'cultureKey' => $ctx->getOption('cultureKey', ['context_key' => $contextKey], 'en'),
                'url'        => $this->modx->makeUrl($resourceId, '', '', 'full'),
                'locale'     => $this->config['meta']['default_alternate_context'] === $ctx->get('key') ? 'x-default' : $ctx->getOption('locale')
            ];

            if (isset($options['alternateTpl']) && !empty($options['alternateTpl'])) {
                $html[] = $this->getChunk($options['alternateTpl'], $alternate);
            }

            $alternates[] = $alternate;
        }

        if (isset($options['alternateTpl']) && !empty($options['alternateTpl'])) {
            return implode(PHP_EOL, $html);
        }

        return $alternates;
    }

    /**
     * Get Babel.
     * @return mixed
     */
    protected function getBabel()
    {
        if (!$this->babel) {
            $this->setBabel();
        }

        return $this->babel;
    }

    /**
     * Set babel.
     */
    protected function setBabel()
    {
        $this->babel = &$this->modx->getService(
            'babel',
            'Babel',
            $this->modx->getOption(
                'babel.core_path',
                null,
                $this->modx->getOption('core_path') . 'components/babel/'
            ) . 'model/babel/'
        );
    }

    /**
     * Determine if babel alternative links should be added.
     * @return bool
     */
    protected function shouldAddBabelAlternativeLinks()
    {
        if ($this->config['sitemap']['babel_add_alternate_links'] === false ||
            !file_exists($this->modx->getOption('babel.core_path', null, $this->modx->getOption('core_path') . 'components/babel/') . 'model/babel/')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Generate sitemap for images.
     *
     * @param $contextKey
     * @param $resources
     * @param $options
     *
     * @return string
     */
    protected function sitemapImages($contextKey, $resources, $options)
    {
        $usedMediaSourceIds = [];
        $resourceIds        = [];
        if ($resources) {
            foreach ($resources as $resource) {
                $resourceIds[] = $resource->get('id');
            }
        }

        /* Get all image tvs of the retrieved resources and return all image tv's chained to resource. */
        $query = $this->modx->newQuery('modTemplateVar');
        $query->select('modTemplateVar.*, Value.*');
        $query->leftJoin('modTemplateVarResource', 'Value', ['modTemplateVar.id = Value.tmplvarid']);
        $query->where([
            'Value.contentid:IN'     => $resourceIds,
            'Value.value:!='         => '',
            'modTemplateVar.type:IN' => ['image', 'migx', 'imagecropper']
        ]);

        if ($imageTVs = $this->modx->getIterator('modTemplateVar', $query)) {
            $query = $this->modx->newQuery('sources.modMediaSourceElement');
            $query->where([
                'object_class'   => 'modTemplateVar',
                'context_key:IN' => $contextKey
            ]);

            $getTVSources = $this->modx->getIterator('sources.modMediaSourceElement', $query);
            $tvSources    = [];
            if ($getTVSources) {
                foreach ($getTVSources as $tvSource) {
                    $tvSources[$tvSource->get('object')] = $tvSource->get('source');
                }
            }

            foreach ($imageTVs as $imageTV) {
                $imageTV = $imageTV->toArray();
                $cid     = $imageTV['contentid'];

                if ($imageTV['type'] === 'migx') {
                    $this->getImagesValuesFromMIGX($cid, $imageTV, $tvSources);
                } elseif($imageTV['type'] === 'imagecropper') {
                    if (($decodedValue = json_decode($imageTV['value'], true)) && isset($decodedValue['image'], $decodedValue['sizes']) && !empty($decodedValue['image']) && count($decodedValue['sizes']) > 0) {
                        /* Add crops, we don't add the source image because that is not being shown on the webpage. */
                        if (isset($decodedValue['sizes']) && count($decodedValue['sizes']) > 0) {
                            foreach ($decodedValue['sizes'] as $size) {
                                $this->images[$cid][] = [
                                    'id'     => $imageTV['id'],
                                    'value'  => $size['image'],
                                    'source' => $tvSources[$imageTV['tmplvarid']]
                                ];
                            }
                        }
                    }
                } else {
                    $this->images[$cid][] = [
                        'id'     => $imageTV['id'],
                        'value'  => $imageTV['value'],
                        'source' => $tvSources[$imageTV['tmplvarid']]
                    ];
                }

                /* Store used mediasource ID's in an array. */
                if (!in_array($tvSources[$imageTV['tmplvarid']], $usedMediaSourceIds)) {
                    $usedMediaSourceIds[] = $tvSources[$imageTV['tmplvarid']];
                }
            }
        }

        $output = '';
        if ($resources) {
            $mediasources = [];

            if (count($usedMediaSourceIds) > 0) {
                foreach ($usedMediaSourceIds as $mediaSourceId) {
                    $this->modx->loadClass('sources.modMediaSource');

                    if ($source = modMediaSource::getDefaultSource($this->modx, $mediaSourceId, false)) {
                        $source->initialize();
                        /*
                         * CDN TV's are saved with full path, therefore only set full path for modFileMediaSource image tv types.
                         */
                        $url                          = ($source->get('class_key') === 'sources.modFileMediaSource') ? rtrim(MODX_SITE_URL, '/') . '/' . ltrim($source->getBaseUrl(), '/') : '';
                        $mediasources[$mediaSourceId] = array_merge(array('full_url' => $url), $source->toArray());
                    }
                }
            }

            foreach ($resources as $resource) {
                $imagesOutput = '';

                if (isset($this->images[$resource->get('id')])) {
                    foreach ($this->images[$resource->get('id')] as $image) {
                        /* Set correct full url for image based on context and mediasource. */
                        $image         = $this->setImageUrl($mediasources, $image);
                        $imagesOutput .= $this->getChunk($options['imageTpl'], array(
                            'url' => $image['value']
                        ));
                    }

                    $output .= $this->getChunk($options['imagesRowTpl'], array(
                        'url'    => $this->modx->makeUrl($resource->get('id'), '', '', 'full'),
                        'images' => $imagesOutput
                    ));
                }
            }
        }

        return $this->getChunk($options['imagesOuterTpl'], ['wrapper' => $output]);
    }

    /**
     * @param $resources
     * @return mixed
     */
    protected function filterResourcesByParentProperties($resources)
    {
        foreach ($resources as $resourceId => $resource) {
            if ($resource->get('parent') > 0) {
                if (!array_key_exists($resource->get('parent'), $resources)) {
                    unset($resources[$resource->get('id')]);
                }
            }
        }

        return $resources;
    }

    /**
     * Build query to retrieve resources.
     *
     * @param $contextKey
     * @param $allowSymlinks
     * @param $options
     *
     * @return mixed
     */
    protected function buildQuery($contextKey, $allowSymlinks, $options)
    {
        $query = $this->modx->newQuery('modResource');
        $query->innerJoin('SeoSuiteResource', 'SeoSuiteResource', 'SeoSuiteResource.resource_id = modResource.id');

        $query->select(
            [
                'modResource.*',
                $this->modx->getSelectColumns(
                    'SeoSuiteResource',
                    'SeoSuiteResource',
                    'SeoSuiteResource.',
                    array_keys($this->modx->getFields('SeoSuiteResource'))
                )
            ]
        );

        $query->where([
            [
                'modResource.context_key:IN' => $contextKey,
                'modResource.published'      => 1,
                'modResource.deleted'        => 0
            ]
        ]);

        /* Exclude pages with noindex and nofollow. */
        $query->where([
            'SeoSuiteResource.index_type'  => 1,
            'SeoSuiteResource.follow_type' => 1
        ]);

        if ($options['type'] !== 'index') {
            $query->where([
                'SeoSuiteResource.sitemap' => true
            ]);
        }

        if (!$allowSymlinks) {
            $query->where(['modResource.class_key:!=' => 'modSymLink']);
        }

        if ($options['type'] === 'index') {
            $parent = $this->modx->resource->get('id');
            $query->where(['modResource.parent' => $parent]);
        }

        if (!empty($options['templates'])) {
            $notAllowedTemplates = [];
            $allowedTemplates    = [];
            $this->parseTemplatesParam($options['templates'], $notAllowedTemplates, $allowedTemplates);

            if (count($notAllowedTemplates) > 0) {
                $query->where(['modResource.template:NOT IN' => $notAllowedTemplates]);
            }

            if (count($allowedTemplates) > 0) {
                $query->where(['modResource.template:IN' => $allowedTemplates]);
            }
        }

        return $query;
    }

    /**
     * Parse templates parameter and set allowed and non-allowed templates as arrays.
     *
     * @param $templates
     * @param $notAllowedTemplates
     * @param $allowedTemplates
     */
    protected function parseTemplatesParam($templates, &$notAllowedTemplates, &$allowedTemplates)
    {
        $templates = explode(',', $templates);
        foreach ($templates as $template) {
            $template = trim($template, ' ');
            $char     = substr($template, 0, 1);
            if ($char === '-') {
                $notAllowedTemplates[] = trim($template, '-');
            } else {
                $allowedTemplates[] = $template;
            }
        }
    }

    /**
     * Set the image URL based on related mediasource.
     *
     * @param $mediasources
     * @param $image
     *
     * @return mixed
     */
    protected function setImageUrl($mediasources, $image)
    {
        if (array_key_exists($image['source'], $mediasources)) {
            $image['value'] = rtrim($mediasources[$image['source']]['full_url'], '/') . '/' . ltrim($image['value'], '/');
        }
        return $image;
    }
}
