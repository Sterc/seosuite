SeoSuite.panel.Migration = function(config) {
    config = config || {};

    Ext.Ajax.timeout = 0;

    Ext.apply(config, {
        id          : 'seosuite-panel-migration',
        style       : 'min-height: 100px;',
        items       : [{
            xtype: 'modx-panel',
            defaults: { border: false ,autoHeight: true, style: 'margin: 8px 0;' },
            border: true,
            hideMode: 'offsets',
            cls: 'x-tab-panel-bwrap main-wrapper',
            items: [{
                html: '<h3>SEO Suite V1</h3>'
            },{
                id: 'seosuite-migration-seosuitev1'
            },{
                id: 'seosuite-migration-status-seosuitev1-redirects',
                cls: 'seosuite-migration-status',
                hidden: true
            },{
                id: 'seosuite-migration-status-seosuitev1-urls',
                cls: 'seosuite-migration-status',
                hidden: true
            },{
                xtype: 'button',
                text: _('seosuite.migration.migrate') + ' ' + 'SEO Suite V1',
                cls: 'primary-button',
                minWidth: 75,
                handler: function () {
                    this.runMigration('seosuitev1-redirects');
                    this.runMigration('seosuitev1-urls');
                },
                scope: this
            },{
                html: '<h3>SEO Pro</h3>',
                style: 'margin-top: 25px;'
            },{
                id: 'seosuite-migration-seopro'
            },{
                id: 'seosuite-migration-status-seopro',
                cls: 'seosuite-migration-status',
                hidden: true
            },{
                xtype: 'button',
                text: _('seosuite.migration.migrate') + ' ' + 'SEO Pro',
                cls: 'primary-button',
                minWidth: 75,
                handler: function () {
                    this.runMigration('seopro');
                },
                scope: this
            },{
                html: '<h3>SEO Tab</h3>',
                style: 'margin-top: 25px;'
            },{
                id: 'seosuite-migration-seotab'
            },{
                id: 'seosuite-migration-status-seotab-redirects',
                cls: 'seosuite-migration-status',
                hidden: true
            },{
                id: 'seosuite-migration-status-seotab-urls',
                cls: 'seosuite-migration-status',
                hidden: true
            },{
                xtype: 'button',
                text: _('seosuite.migration.migrate') + ' ' + 'SEO Tab',
                cls: 'primary-button',
                minWidth: 75,
                handler: function () {
                    this.runMigration('seotab-redirects', 5000);
                    this.runMigration('seotab-urls', 5000);
                },
                scope: this
            }]
        }],
        listeners: {
            'afterrender': {fn: this.checkStatus, scope:this }
        }
    });

    SeoSuite.panel.Migration.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.panel.Migration, MODx.Panel, {
    checkStatus: function() {
        this.mask = new Ext.LoadMask(Ext.get('seosuite-panel-migration'), {msg:_('loading')});
        this.mask.show();

        MODx.Ajax.request({
            url         : SeoSuite.config.connector_url,
            params      : {
                action  : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Migration\\Status'
            },
            listeners   : {
                'success' : {
                    fn  : function (response) {
                        this.mask.hide();

                        var seosuiteV1Status    = _('seosuite.migration.seosuitev1.empty');
                        var seoproStatus        = _('seosuite.migration.seopro.empty');
                        var seotabStatus        = _('seosuite.migration.seotab.empty');

                        if (response.results) {
                            if (response.results.seosuitev1) {
                                seosuiteV1Status = response.results.seosuitev1;
                            }

                            if (response.results.seopro) {
                                seoproStatus = response.results.seopro;
                            }

                            if (response.results.seotab) {
                                seotabStatus = response.results.seotab;
                            }
                        }

                        Ext.getCmp('seosuite-migration-seosuitev1').update(seosuiteV1Status);
                        Ext.getCmp('seosuite-migration-seopro').update(seoproStatus);
                        Ext.getCmp('seosuite-migration-seotab').update(seotabStatus);
                    },
                    scope       : this
                }
            }
        });
    },
    runMigration: function(source, limit = 1000, offset = 0) {
        Ext.getCmp('seosuite-migration-status-' + source).show();

        var mask = new Ext.LoadMask(Ext.get('seosuite-migration-status-' + source), {msg:_('loading')});
        mask.show();

        MODx.Ajax.request({
            url             : SeoSuite.config.connector_url,
            params          : {
                action      : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Migration\\Migrate',
                source      : source,
                limit       : limit,
                offset      : offset
            },
            listeners   : {
                'success': {
                    fn: function (response) {
                        mask.hide();

                        if (response.results && response.results.message) {
                            Ext.getCmp('seosuite-migration-status-' + source).add({
                                html: response.results.message
                            });

                            Ext.getCmp('seosuite-migration-status-' + source).doLayout();

                            if (response.results.offset && response.results.offset > 0) {
                                this.runMigration(source, limit, response.results.offset);
                            }
                        }
                    },
                    scope: this
                }
            }
        });
    }
});

Ext.reg('seosuite-panel-migration', SeoSuite.panel.Migration);