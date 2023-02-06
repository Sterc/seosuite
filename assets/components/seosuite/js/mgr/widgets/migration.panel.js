SeoSuite.panel.Migration = function(config) {
    config = config || {};

    Ext.apply(config, {
        id          : 'seosuite-panel-migration',
        items       : [{
            xtype: 'modx-panel',
            defaults: { border: false ,autoHeight: true, style: 'margin: 5px 0;' },
            border: true,
            hideMode: 'offsets',
            cls: 'x-tab-panel-bwrap main-wrapper',
            items: [{
                html: '<h3>SEO Suite V1</h3>'
            },{
                id: 'seosuite-migration-status-seosuitev1',
                html: '',
            },{
                xtype: 'button',
                id: 'seosuite-migration-seosuitev1',
                text: _('seosuite.migration.migrate') + ' ' + 'SEO Suite V1',
                cls: 'primary-button',
                minWidth: 75,
                handler: this.migrateSeosuiteV1,
                scope: this
            },{
                html: '<h3>SEO Pro</h3>',
                style: 'margin-top: 25px;'
            },{
                id: 'seosuite-migration-status-seopro',
                html: ''
            },{
                xtype: 'button',
                id: 'seosuite-migration-seopro',
                text: _('seosuite.migration.migrate') + ' ' + 'SEO Pro',
                cls: 'primary-button',
                minWidth: 75,
                handler: this.migrateSeoPro,
                scope: this
            },{
                html: '<h3>SEO Tab</h3>',
                style: 'margin-top: 25px;'
            },{
                id: 'seosuite-migration-status-seotab',
                html: ''
            },{
                xtype: 'button',
                id: 'seosuite-migration-seotab',
                text: _('seosuite.migration.migrate') + ' ' + 'SEO Tab',
                cls: 'primary-button',
                minWidth: 75,
                handler: this.migrateSeoTab,
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

                        Ext.getCmp('seosuite-migration-status-seosuitev1').update(seosuiteV1Status);
                        Ext.getCmp('seosuite-migration-status-seopro').update(seoproStatus);
                        Ext.getCmp('seosuite-migration-status-seotab').update(seotabStatus);
                    },
                    scope       : this
                }
            }
        });
    },
    migrateSeosuiteV1: function() {
        MODx.Ajax.request({
            url         : SeoSuite.config.connector_url,
            params      : {
                action  : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Migration\\Migrate',
                source  : 'seosuitev1'
            },
            listeners   : {
                'success': {
                    fn: function (response) {
                        if (response.total) {
                            var message;
                            if (r.total == 0) {
                                message = '<p>'+_('formit.migrate_success_msg')+'</p>';
                            } else {
                                // Processing redirects
                                message = '<p>'+_('formit.migrate_running')+'</p>';
                                Ext.getCmp('formit-migrate-panel').fireEvent('render');
                            }
                            Ext.getCmp('formit-migrate-panel-status').update(message);
                        }
                    },
                    scope: this
                }
            }
        });
    },
    migrateSeoPro: function() {
        MODx.Ajax.request({
            url         : SeoSuite.config.connector_url,
            params      : {
                action  : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Migration\\Migrate',
                source  : 'seopro'
            },
            listeners   : {
                'success': {
                    fn: function (response) {
                        if (response.total) {
                            var message;
                            if (r.total == 0) {
                                message = '<p>'+_('formit.migrate_success_msg')+'</p>';
                            } else {
                                // Processing redirects
                                message = '<p>'+_('formit.migrate_running')+'</p>';
                                Ext.getCmp('formit-migrate-panel').fireEvent('render');
                            }
                            Ext.getCmp('formit-migrate-panel-status').update(message);
                        }
                    },
                    scope: this
                }
            }
        });
    },
    migrateSeoTab: function() {
        this.mask = new Ext.LoadMask(Ext.get('seosuite-panel-migration'), {msg:_('loading')});
        this.mask.show();

        MODx.Ajax.request({
            url         : SeoSuite.config.connector_url,
            params      : {
                action  : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Migration\\Migrate',
                source  : 'seotab'
            },
            listeners   : {
                'success': {
                    fn: function (response) {
                        this.mask.hide();

                        if (response.total) {
                            var message;
                            if (r.total == 0) {
                                message = '<p>'+_('formit.migrate_success_msg')+'</p>';
                            } else {
                                // Processing redirects
                                message = '<p>'+_('formit.migrate_running')+'</p>';
                                Ext.getCmp('formit-migrate-panel').fireEvent('render');
                            }
                            Ext.getCmp('formit-migrate-panel-status').update(message);
                        }
                    },
                    scope: this
                }
            }
        });
    }
});

Ext.reg('seosuite-panel-migration', SeoSuite.panel.Migration);