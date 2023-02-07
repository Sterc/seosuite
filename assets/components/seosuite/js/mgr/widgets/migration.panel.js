SeoSuite.panel.Migration = function(config) {
    config = config || {};

    Ext.apply(config, {
        id          : 'seosuite-panel-migration',
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
                id: 'seosuite-migration-status-seosuitev1',
                cls: 'seosuite-migration-status',
                hidden: true
            },{
                xtype: 'button',
                text: _('seosuite.migration.migrate') + ' ' + 'SEO Suite V1',
                cls: 'primary-button',
                minWidth: 75,
                handler: function () {
                    this.runMigration('seosuitev1');
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
                id: 'seosuite-migration-status-seotab',
                cls: 'seosuite-migration-status',
                hidden: true
            },{
                xtype: 'button',
                text: _('seosuite.migration.migrate') + ' ' + 'SEO Tab',
                cls: 'primary-button',
                minWidth: 75,
                handler: function () {
                    this.runMigration('seotab');
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
        MODx.Ajax.request({
            url         : SeoSuite.config.connector_url,
            params      : {
                action  : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Migration\\Status'
            },
            listeners   : {
                'success' : {
                    fn  : function (response) {
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
    runMigration: function(source) {
        this.mask = new Ext.LoadMask(Ext.get('seosuite-panel-migration'), {msg:_('loading')});
        this.mask.show();

        MODx.Ajax.request({
            url         : SeoSuite.config.connector_url,
            params      : {
                action  : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Migration\\Migrate',
                source  : source
            },
            listeners   : {
                'success': {
                    fn: function (response) {
                        this.mask.hide();

                        if (response.results && response.results.message) {
                            Ext.getCmp('seosuite-migration-status-' + source).show();
                            Ext.getCmp('seosuite-migration-status-' + source).update(response.results.message);
                        }
                    },
                    scope: this
                }
            }
        });
    }
});

Ext.reg('seosuite-panel-migration', SeoSuite.panel.Migration);