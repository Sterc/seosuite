<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteTabSeo extends SeoSuitePlugin
{
    /**
     * @access public.
     * @param Object $event.
     */
    public function onDocFormPrerender($event)
    {
        $this->modx->controller->addCss($this->seosuite->config['css_url'] . 'mgr/seosuite.css');

        $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/seosuite.js');

        $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/extras/extras.js');

        $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/widgets/resource.tab_seo.js');

        if (is_array($this->seosuite->config['lexicons'])) {
            foreach ($this->seosuite->config['lexicons'] as $lexicon) {
                $this->modx->controller->addLexiconTopic($lexicon);
            }
        } else {
            $this->modx->controller->addLexiconTopic($this->seosuite->config['lexicons']);
        }

        $properties = [
            'seosuite_index_type'           => $this->seosuite->config['tab_seo']['default_index_type'],
            'seosuite_follow_type'          => $this->seosuite->config['tab_seo']['default_follow_type'],
            'seosuite_searchable'           => 1,
            'seosuite_override_uri'         => 0,
            'seosuite_uri'                  => '',
            'seosuite_sitemap'              => $this->seosuite->config['tab_seo']['default_sitemap'],
            'seosuite_sitemap_prio'         => 'normal',
            'seosuite_sitemap_changefreq'   => '',
            'seosuite_canonical'            => 0,
            'seosuite_canonical_uri'        => ''
        ];

        $resource =& $event->params['resource'];

        if ($resource) {
            $properties = array_merge($properties, [
                'seosuite_searchable'   => $resource->get('searchable') ? 1 : 0,
                'seosuite_override_uri' => $resource->get('uri_override') ? 1 : 0,
                'seosuite_uri'          => $resource->get('uri')
            ]);

            $seoSuiteResource = $this->seosuite->getSeoSuiteResourceProperties($resource->get('id'));

            if ($seoSuiteResource) {
                $properties = array_merge($properties, [
                    'seosuite_index_type'           => $seoSuiteResource->get('index_type'),
                    'seosuite_follow_type'          => $seoSuiteResource->get('follow_type'),
                    'seosuite_sitemap'              => $seoSuiteResource->get('sitemap'),
                    'seosuite_sitemap_prio'         => $seoSuiteResource->get('sitemap_prio'),
                    'seosuite_sitemap_changefreq'   => $seoSuiteResource->get('sitemap_changefreq'),
                    'seosuite_canonical'            => $seoSuiteResource->get('canonical'),
                    'seosuite_canonical_uri'        => $seoSuiteResource->get('canonical_uri')
                ]);
            }
        }

        $this->modx->controller->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                SeoSuite.config = ' . $this->modx->toJSON($this->seosuite->config) . ';
                
                SeoSuite.record = ' . $this->modx->toJSON($properties) . ';
            });
        </script>');
    }

    /**
     * @access public.
     * @param Object $event.
     */
    public function onDocFormSave($event)
    {
        $resource =& $event->params['resource'];

        if ($resource) {
            $properties = [
                'index_type'            => $this->seosuite->config['tab_seo']['default_index_type'],
                'follow_type'           => $this->seosuite->config['tab_seo']['default_follow_type'],
                'sitemap'               => 0,
                'sitemap_prio'          => 'normal',
                'sitemap_changefreq'    => '',
                'canonical'             => 0,
                'canonical_uri'         => ''
            ];

            foreach (array_keys($properties) as $key) {
                if (isset($_POST['seosuite_' . $key])) {
                    $properties[$key] = $_POST['seosuite_' . $key];
                }
            }

            $this->seosuite->setSeoSuiteResourceProperties($resource->get('id'), $properties);
        }
    }

    /**
     * @TODO DUPLICATE CHILDREN
     *
     * @access public.
     * @param Object $event.
     */
    public function onResourceDuplicate($event)
    {
        $oldResource =& $event->params['oldResource'];
        $newResource =& $event->params['newResource'];

        if ($oldResource && $newResource) {
            $properties = $this->seosuite->getSeoSuiteResourceProperties($oldResource->get('id'));

            if ($properties) {
                $properties = array_merge($properties->toArray(), [
                    'resource_id' => $newResource->get('id')
                ]);

                $this->seosuite->setSeoSuiteResourceProperties($newResource->get('id'), $properties);
            }
        }
    }

    /**
     * @access public.
     * @param Object $event.
     */
    public function onEmptyTrash($event)
    {
        foreach ((array) $event->params['ids'] as $id) {
            $this->seosuite->removeSeoSuiteResourceProperties($id);
        }
    }

    public function onBeforeDocFormSave()
    {

    }

    public function onLoadWebDocument()
    {

    }

    public function onPageNotFound()
    {

    }

    public function onResourceBeforeSort()
    {

    }

    public function onManagerPageBeforeRender()
    {

    }
}