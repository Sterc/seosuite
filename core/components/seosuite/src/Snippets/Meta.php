<?php
namespace Sterc\SeoSuite\Snippets;

use Sterc\SeoSuite\Snippets\Base;
use Sterc\SeoSuite\Model\SeoSuiteResource;
use Sterc\SeoSuite\Model\SeoSuiteSocial;
use MODX\Revolution\modResource;
use MODX\Revolution\Sources\modMediaSource;

class Meta extends Base
{
    const PHS_PREFIX = 'ss_meta';

    /**
     * @param array $properties
     */
    public function process(array $properties = [])
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

        if (!$ssResource = $this->modx->getObject(SeoSuiteResource::class, ['resource_id' => $id])) {
            return false;
        }

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
            'tpl'   => $tplLink
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

        $ssSocial = $this->modx->getObject(SeoSuiteSocial::class, ['resource_id' => $id]);
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

        $resourceArray = ($modResource = $this->modx->getObject(modResource::class, $id)) ? $modResource->toArray() : [];
        if ($alternatives = $this->getAlternateLinks($modResource)) {
            $values = [];
            $alternateHTML = '';

            foreach ($alternatives as $alternative) {
                $values[] = $this->getChunk($tplLink, [
                    'name'     => 'alternate',
                    'value'    => $alternative['url'],
                    'hreflang' => str_replace(['_', '.utf8'], ['-', ''], $alternative['locale'])
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
                $ms_default = $this->modx->getObject(modMediaSource::class, $ms_default_id);
                if ($ms_default) {
                    $ms_base_url = $ms_default->get('properties')['baseUrl']['value'];
                    $imageUrl = trim($ms_base_url, '/') . '/' . trim($item['value'], '/');
                    $item['value'] = rtrim($this->modx->makeUrl($this->modx->getOption('site_start'), null, null, 'full'), '/') . '/' . trim($imageUrl, '/');
                }
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
}