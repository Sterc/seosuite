<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

require_once dirname(__DIR__) . '/index.class.php';

class SeoSuiteHomeManagerController extends SeoSuiteManagerController
{
    /**
     * @access public.
     */
    public function loadCustomCssJs()
    {
        $this->addJavascript($this->modx->seosuite->config['js_url'] . 'mgr/extras/extras.js');
        $this->addJavascript($this->modx->seosuite->config['js_url'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->modx->seosuite->config['js_url'] . 'mgr/widgets/urls.grid.js');
        $this->addJavascript($this->modx->seosuite->config['js_url'] . 'mgr/widgets/redirects.grid.js');
        $this->addLastJavascript($this->modx->seosuite->config['js_url'] . 'mgr/sections/home.js');
    }

    /**
     * @access public.
     * @return String.
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('seosuite');
    }

    /**
     * @access public.
     * @return String.
     */
    public function getTemplateFile()
    {
        return $this->modx->seosuite->config['templates_path'] . 'home.tpl';
    }
}
