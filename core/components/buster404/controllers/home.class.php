<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';
/**
 * Loads the home page.
 *
 * @package buster404
 * @subpackage controllers
 */
class Buster404HomeManagerController extends Buster404BaseManagerController
{
    public function process(array $scriptProperties = array())
    {
    }
    public function getPageTitle()
    {
        return $this->modx->lexicon('buster404');
    }
    public function loadCustomCssJs()
    {
        $this->addJavascript($this->buster404->getOption('jsUrl').'mgr/widgets/urls.grid.js');
        $this->addJavascript($this->buster404->getOption('jsUrl').'mgr/widgets/home.panel.js');
        $this->addLastJavascript($this->buster404->getOption('jsUrl').'mgr/sections/home.js');
    }

    public function getTemplateFile()
    {
        return $this->buster404->getOption('templatesPath').'home.tpl';
    }
}
