<?php
require_once __DIR__ . '/model/seosuite/seosuite.class.php';
/**
 * @package seosuite
 */

abstract class SeoSuiteBaseManagerController extends modExtraManagerController
{
    /** @var SeoSuite $seosuite */
    public $seosuite;

    public function initialize()
    {
        $this->seosuite = new SeoSuite($this->modx);

        $this->addCss($this->seosuite->getOption('cssUrl') . 'mgr.css');
        $this->addJavascript($this->seosuite->getOption('jsUrl') . 'mgr/seosuite.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            SeoSuite.config = ' . $this->modx->toJSON($this->seosuite->options) . ';
            SeoSuite.config.connector_url = "' . $this->seosuite->getOption('connectorUrl') . '";
        });
        </script>');
        
        parent::initialize();
    }

    public function getLanguageTopics()
    {
        return ['seosuite:default'];
    }

    public function checkPermissions()
    {
        return true;
    }
}