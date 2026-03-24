<?php
namespace Sterc\SeoSuite\Controllers;

class Home extends Base
{
    /**
     * @access public.
     */
    public function loadCustomCssJs()
    {
        $this->addJavascript($this->seosuite->config['js_url'] . 'mgr/extras/extras.js?v=v' . $this->seosuite->getOption('version'));
        $this->addJavascript($this->seosuite->config['js_url'] . 'mgr/widgets/home.panel.js?v=v' . $this->seosuite->getOption('version'));
        $this->addJavascript($this->seosuite->config['js_url'] . 'mgr/widgets/migration.panel.js?v=v' . $this->seosuite->getOption('version'));
        $this->addJavascript($this->seosuite->config['js_url'] . 'mgr/widgets/urls.grid.js?v=v' . $this->seosuite->getOption('version'));
        $this->addJavascript($this->seosuite->config['js_url'] . 'mgr/widgets/redirects.grid.js?v=v' . $this->seosuite->getOption('version'));
        $this->addLastJavascript($this->seosuite->config['js_url'] . 'mgr/sections/home.js?v=v' . $this->seosuite->getOption('version'));
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
