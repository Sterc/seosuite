SeoSuite.panel.Home = function(config) {
    config = config || {};

    Ext.apply(config, {
        id          : 'seosuite-panel-home',
        cls         : 'container',
        items       : [{
            html        : '<h2>' + _('seosuite') + '</h2>',
            cls         : 'modx-page-header'
        }, {
            xtype       : 'modx-tabs',
            items       : [{
                title       : _('seosuite.urls'),
                items       : [{
                    html        : '<p>' + _('seosuite.urls_desc') + '</p>',
                    bodyCssClass : 'panel-desc'
                }, {
                    html            : parseInt(MODx.config.friendly_urls) === 0 ? '<p>' + _('seosuite.friendly_urls_disabled') + '</p>' : '',
                    cls             : parseInt(MODx.config.friendly_urls) === 0 ? 'modx-config-error panel-desc' : ''
                }, {
                    xtype       : 'seosuite-grid-urls',
                    cls         : 'main-wrapper',
                    preventRender : true,
                    refreshGrid : 'seosuite-grid-redirects'
                }]
            }, {
                title       : _('seosuite.redirects'),
                items       : [{
                    html        : '<p>' + _('seosuite.redirects_desc') + '</p>',
                    bodyCssClass : 'panel-desc'
                }, {
                    html            : parseInt(MODx.config.friendly_urls) === 0 ? '<p>' + _('seosuite.friendly_urls_disabled') + '</p>' : '',
                    cls             : parseInt(MODx.config.friendly_urls) === 0 ? 'modx-config-error panel-desc' : ''
                }, {
                    xtype       : 'seosuite-grid-redirects',
                    cls         : 'main-wrapper',
                    preventRender : true
                }]
            }]
        }]
    });

    SeoSuite.panel.Home.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.panel.Home, MODx.Panel);

Ext.reg('seosuite-panel-home', SeoSuite.panel.Home);
