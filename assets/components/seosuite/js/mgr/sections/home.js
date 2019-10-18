Ext.onReady(function() {
    MODx.load({
        xtype : 'seosuite-page-home'
    });
});

SeoSuite.page.Home = function(config) {
    config = config || {};

    config.buttons = [];

    if (SeoSuite.config.branding_url) {
        config.buttons.push({
            text        : 'SeoSuite ' + SeoSuite.config.version,
            cls         : 'x-btn-branding',
            handler     : this.loadBranding
        });
    }

    if (SeoSuite.config.branding_url_help) {
        config.buttons.push('-', {
            text        : _('help_ex'),
            handler     : MODx.loadHelpPane,
            scope       : this
        });
    }

    Ext.applyIf(config, {
        components  : [{
            xtype       : 'seosuite-panel-home',
            renderTo    : 'seosuite-panel-home-div'
        }]
    });

    SeoSuite.page.Home.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.page.Home, MODx.Component, {
    loadBranding: function(btn) {
        window.open(SeoSuite.config.branding_url);
    }
});

Ext.reg('seosuite-page-home', SeoSuite.page.Home);