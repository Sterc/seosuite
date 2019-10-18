<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

class SeoSuiteTabSocial extends SeoSuitePlugin
{
    /**
     * @access public.
     * @return Boolean.
     */
    protected function hasPermission()
    {
        if (isset($this->seosuite->config['tab_social']['permission'])) {
            return $this->seosuite->config['tab_social']['permission'];
        }

        return false;
    }

    /**
     * @access public.
     * @param Object $event.
     * @return void.
     */
    public function onDocFormPrerender($event)
    {
        if (!$this->hasPermission()) {
            return null;
        }

        $this->modx->controller->addCss($this->seosuite->config['css_url'] . 'mgr/seosuite.css');

        $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/seosuite.js');

        $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/extras/extras.js');

        $this->modx->controller->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/resource/resource.tab_social.js');

        if (is_array($this->seosuite->config['lexicons'])) {
            foreach ($this->seosuite->config['lexicons'] as $lexicon) {
                $this->modx->controller->addLexiconTopic($lexicon);
            }
        } else {
            $this->modx->controller->addLexiconTopic($this->seosuite->config['lexicons']);
        }

        $this->modx->controller->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                SeoSuite.config = ' . $this->modx->toJSON($this->seosuite->config) . ';
            });
        </script>');
    }
}