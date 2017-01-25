<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';
/**
 * Loads the home page.
 *
 * @package seosuite
 * @subpackage controllers
 */
class SeoSuiteHomeManagerController extends SeoSuiteBaseManagerController
{
    public function process(array $scriptProperties = array())
    {
    }
    public function getPageTitle()
    {
        return $this->modx->lexicon('seosuite');
    }
    public function loadCustomCssJs()
    {
        $this->addJavascript($this->seosuite->getOption('jsUrl').'mgr/widgets/urls.grid.js');
        $this->addJavascript($this->seosuite->getOption('jsUrl').'mgr/widgets/home.panel.js');
        $this->addLastJavascript($this->seosuite->getOption('jsUrl').'mgr/sections/home.js');
    }

    public function getTemplateFile()
    {
        return $this->seosuite->getOption('templatesPath').'home.tpl';
    }
}
