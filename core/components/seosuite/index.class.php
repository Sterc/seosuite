<?php

/**
 * SeoSuite
 *
 * Copyright 2019 by Sterc <modx@sterc.com>
 */

abstract class SeoSuiteManagerController extends modExtraManagerController
{
    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('seosuite', 'SeoSuite', $this->modx->getOption('seosuite.core_path', null, $this->modx->getOption('core_path') . 'components/seosuite/') . 'model/seosuite/');

        $this->addCss($this->modx->seosuite->config['css_url'] . 'mgr/seosuite.css');

        $this->addJavascript($this->modx->seosuite->config['js_url'] . 'mgr/seosuite.js');

        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                MODx.config.help_url = "' . $this->modx->seosuite->getHelpUrl() . '";
            
                SeoSuite.config = ' . $this->modx->toJSON(array_merge($this->modx->seosuite->config, [
                    'branding_url'          => $this->modx->seosuite->getBrandingUrl(),
                    'branding_url_help'     => $this->modx->seosuite->getHelpUrl()
                ])) . ';
            });
        </script>');
        
        parent::initialize();
    }

    /**
     * @access public.
     * @return Array.
     */
    public function getLanguageTopics()
    {
        return $this->modx->seosuite->config['lexicons'];
    }

    /**
     * @access public.
     * @returns Boolean.
     */
    public function checkPermissions()
    {
        return $this->modx->hasPermission('seosuite');
    }
}

class IndexManagerController extends SeoSuiteManagerController
{
    /**
     * @access public.
     * @return String.
     */
    public static function getDefaultController()
    {
        return 'home';
    }
}
