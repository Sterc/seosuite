<?php
namespace Sterc\SeoSuite\Controllers;

class Home extends Base
{
    /**
     * @access public.
     */
    public function loadCustomCssJs()
    {
        $this->addJavascript($this->seosuite->config['js_url'] . 'mgr/extras/extras.js');
        $this->addJavascript($this->seosuite->config['js_url'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->seosuite->config['js_url'] . 'mgr/widgets/urls.grid.js');
        $this->addJavascript($this->seosuite->config['js_url'] . 'mgr/widgets/redirects.grid.js');
        $this->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/sections/home.js');
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
        return $this->seosuite->config['templates_path'] . 'home.tpl';
    }
}