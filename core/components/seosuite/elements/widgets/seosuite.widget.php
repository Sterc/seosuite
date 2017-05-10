<?php
/**
 * @package modx
 * @subpackage dashboard
 */
/**
 * Renders a grid of recently edited resources by the active user
 *
 * @package modx
 * @subpackage dashboard
 */
class modDashboardWidgetSeoSuiteUrls extends modDashboardWidgetInterface {
    public function render() {
        $corePath = $this->modx->getOption(
            'seosuite.core_path',
            null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/seosuite/'
        );
        $seoSuite = $this->modx->getService(
            'seosuite',
            'SeoSuite',
            $corePath . 'model/seosuite/',
            array(
                'core_path' => $corePath
            )
        );
        if (!($seoSuite instanceof SeoSuite)) {
            return;
        }

        $jsUrl = $seoSuite->options['jsUrl'];
        $this->modx->regClientStartupHTMLBlock(
            '<script type="text/javascript" src="'.$jsUrl.'mgr/seosuite.js" ></script>
            <script type="text/javascript" src="'.$jsUrl.'mgr/widgets/dashboardwidget.grid.js" ></script>
            <script type="text/javascript">Ext.onReady(function() {
                SeoSuite.config = '.$this->modx->toJSON($seoSuite->options).';
                SeoSuite.config.connector_url = "'.$seoSuite->getOption('connectorUrl').'";
                MODx.load({
                    xtype: "seosuite-dashboard-grid-urls"
                    ,renderTo: "seosuite-grid-urls"
                });
            });</script>'
        );
        return '<div id="seosuite-grid-urls"></div>';
    }
}
return 'modDashboardWidgetSeoSuiteUrls';
