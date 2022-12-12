<?php
use MODX\Revolution\modDashboardWidgetInterface;

/**
 * @package seosuite
 */
class modDashboardWidgetSeoSuiteUrls extends modDashboardWidgetInterface
{
    /**
     * @return string|void
     */
    public function render()
    {
        $this->modx->seosuite = $this->modx->services->get('seosuite');

        $langs = $this->modx->seosuite->getLangs();
        $jsUrl = $this->modx->seosuite->config['js_url'];

        $this->modx->regClientStartupHTMLBlock(
            '<script type="text/javascript" src="' . $jsUrl . 'mgr/seosuite.js" ></script>
            <script type="text/javascript" src="' . $jsUrl . 'mgr/widgets/dashboardwidget.grid.js" ></script>
            <script type="text/javascript">Ext.onReady(function () {
                ' . $langs . '
                SeoSuite.config = ' . $this->modx->toJSON($this->modx->seosuite->config) . ';
                SeoSuite.config.connector_url = "' . $this->modx->seosuite->config['connector_url'] . '";
                MODx.load({
                    xtype    : "seosuite-dashboard-grid-urls",
                    renderTo : "seosuite-grid-urls"
                });
            });</script>'
        );
        return '
            <p>[[%seosuite.widget_desc]]</p><br />
            <div id="seosuite-grid-urls"></div>
        ';
    }
}

return 'modDashboardWidgetSeoSuiteUrls';
