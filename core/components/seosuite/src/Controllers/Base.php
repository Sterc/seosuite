<?php
namespace Sterc\SeoSuite\Controllers;

use MODX\Revolution\modExtraManagerController;
use MODX\Revolution\modX;
use Sterc\SeoSuite\SeoSuite;

class Base extends modExtraManagerController
{
    /**
     * @var SeoSuite
     */
    protected $seosuite;

    public function __construct(modX $modx, $config = [])
    {
        parent::__construct($modx, $config);

        $this->seosuite = new SeoSuite($modx);
    }

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->addCss($this->seosuite->config['css_url'] . 'mgr/seosuite.css');

        $this->addJavascript($this->seosuite->config['js_url'] . 'mgr/seosuite.js');

        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                MODx.config.help_url = "' . $this->seosuite->getHelpUrl() . '";

                SeoSuite.config = ' . $this->modx->toJSON(array_merge($this->seosuite->config, [
                    'branding_url'          => $this->seosuite->getBrandingUrl(),
                    'branding_url_help'     => $this->seosuite->getHelpUrl()
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
        return $this->seosuite->config['lexicons'];
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
