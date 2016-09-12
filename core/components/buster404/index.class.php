<?php
require_once dirname(__FILE__) . '/model/buster404/buster404.class.php';
/**
 * @package buster404
 */

abstract class Buster404BaseManagerController extends modExtraManagerController {
    /** @var Buster404 $buster404 */
    public $buster404;
    public function initialize() {
        $this->buster404 = new Buster404($this->modx);

        $this->addCss($this->buster404->getOption('cssUrl').'mgr.css');
        $this->addJavascript($this->buster404->getOption('jsUrl').'mgr/buster404.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Buster404.config = '.$this->modx->toJSON($this->buster404->options).';
            Buster404.config.connector_url = "'.$this->buster404->getOption('connectorUrl').'";
        });
        </script>');
        
        parent::initialize();
    }
    public function getLanguageTopics() {
        return array('buster404:default');
    }
    public function checkPermissions() { return true;}
}