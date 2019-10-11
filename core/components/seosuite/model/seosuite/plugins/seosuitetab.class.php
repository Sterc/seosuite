<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteTab extends SeoSuitePlugin
{
    /**
     * @access public.
     * @param Object $event.
     */
    public function onDocFormPrerender($event)
    {
        $this->modx->regClientStartupScript($this->seosuite->config['js_url'] . 'mgr/seosuite.js');

        $this->modx->regClientStartupScript($this->seosuite->config['js_url'] . 'mgr/extras/extras.js');

        $this->modx->regClientStartupScript($this->seosuite->config['js_url'] . 'mgr/widgets/resource.tab.js');

        if (is_array($this->seosuite->config['lexicons'])) {
            foreach ($this->seosuite->config['lexicons'] as $lexicon) {
                $this->modx->controller->addLexiconTopic($lexicon);
            }
        } else {
            $this->modx->controller->addLexiconTopic($this->seosuite->config['lexicons']);
        }

        $properties = [
            'seosuite_index_type'   => '',
            'seosuite_follow_type'  => '',
            'seosuite_searchable'   => 1,
            'seosuite_override_uri' => 0,
            'seosuite_uri'          => ''
        ];

        $resource =& $event->params['resource'];

        if ($resource) {
            $properties = array_merge($properties, $resource->getProperties('seosuite'), [
                'seosuite_searchable'   => $resource->get('searchable') ? 1 : 0,
                'seosuite_override_uri' => $resource->get('uri_override') ? 1 : 0,
                'seosuite_uri'          => $resource->get('uri')
            ]);
        }

        $this->modx->controller->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                SeoSuite.config = ' . $this->modx->toJSON($this->seosuite->config) . ';
                
                SeoSuite.record = ' . $this->modx->toJSON($properties) . ';
            });
        </script>');
    }

    public function onBeforeDocFormSave()
    {

    }

    public function onDocFormSave()
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

    public function onResourceDuplicate()
    {

    }

    public function onManagerPageBeforeRender()
    {

    }

    public function onEmptyTrash()
    {

    }
}