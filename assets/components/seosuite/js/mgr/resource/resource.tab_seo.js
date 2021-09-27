Ext.onReady(function() {
    var panel = Ext.getCmp('modx-resource-tabs');

    if (panel) {
        panel.add({
            xtype : 'seosuite-panel-seo-tab'
        });
    }
});

SeoSuite.panel.SeoTab = function(config) {
    config = config || {};

    var tabs = [];

    tabs.push({
        title       : _('seosuite.tab_seo.tab_searchable'),
        layout      : 'form',
        labelAlign  : 'top',
        labelSeparator : '',
        items       : [{
            layout      : 'column',
            labelAlign  : 'top',
            defaults    : {
                layout      : 'form',
                labelSeparator : ''
            },
            items       : [{
                columnWidth : .5,
                items       : [{
                    xtype       : 'radiogroup',
                    fieldLabel  : _('seosuite.tab_seo.label_index'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_seo.label_index_desc'),
                    anchor      : '100%',
                    id          : 'seosuite-seo-index',
                    columns     : 1,
                    items       : [{
                        boxLabel    : _('yes') + ' <em>(' + _('seosuite.tab_seo.index_type_index') + ')</em>',
                        name        : 'seosuite_index_type',
                        inputValue  : 1,
                        checked     : SeoSuite.record.seosuite_index_type === true
                    }, {
                        boxLabel    : _('no') + ' <em>(' + _('seosuite.tab_seo.index_type_noindex') + ')</em>',
                        name        : 'seosuite_index_type',
                        inputValue  : 0,
                        checked     : SeoSuite.record.seosuite_index_type === false
                    }],
                    listeners   : {
                        'change'    : function (tf) {
                            var titleField = Ext.getCmp('modx-resource-pagetitle');

                            if (titleField) {
                                titleField.fireEvent('change', titleField);
                            }
                        }
                    }
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_seo.label_index_desc'),
                    cls         : 'desc-under'
                }]
            }, {
                columnWidth : .5,
                items       : [{
                    xtype       : 'radiogroup',
                    fieldLabel  : _('seosuite.tab_seo.label_follow'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_seo.label_follow_desc'),
                    anchor      : '100%',
                    columns     : 1,
                    items       : [{
                        boxLabel    : _('yes') + ' <em>(' + _('seosuite.tab_seo.follow_type_follow') + ')</em>',
                        name        : 'seosuite_follow_type',
                        inputValue  : 1,
                        checked     : SeoSuite.record.seosuite_follow_type === true
                    }, {
                        boxLabel    : _('no') + ' <em>(' + _('seosuite.tab_seo.follow_type_nofollow') + ')</em>',
                        name        : 'seosuite_follow_type',
                        inputValue  : 0,
                        checked     : SeoSuite.record.seosuite_follow_type === false
                    }]
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_seo.label_follow_desc'),
                    cls         : 'desc-under'
                }]
            }]
        }, {
            xtype       : 'checkbox',
            boxLabel    : _('seosuite.tab_seo.label_searchable'),
            name        : 'seosuite_searchable',
            anchor      : '100%',
            inputValue  : 1,
            checked     : SeoSuite.record.seosuite_searchable,
            listeners   : {
                check       : {
                    fn          : function(tf) {
                        var searchable = Ext.getCmp('modx-resource-searchable');

                        if (searchable) {
                            searchable.setValue(tf.getValue());
                        }
                    },
                    scope       : this
                }
            }
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.tab_seo.label_searchable_desc'),
            cls         : 'desc-under'
        }, {
            xtype       : 'checkbox',
            boxLabel    : _('seosuite.tab_seo.label_override_uri', {
                site_url    : MODx.config.site_url
            }),
            name        : 'seosuite_override_uri',
            anchor      : '100%',
            inputValue  : 1,
            checked     : SeoSuite.record.seosuite_override_uri,
            listeners   : {
                check       : {
                    fn          : this.onToggleFreezeUri,
                    scope       : this
                },
                afterrender : {
                    fn          : this.onToggleFreezeUri,
                    scope       : this
                }
            }
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.tab_seo.label_override_uri_desc', {
                site_url    : MODx.config.site_url
            }),
            cls         : 'desc-under'
        }, {
            id          : 'seosuite-freeze-uri-container',
            layout      : 'form',
            labelSeparator : '',
            items       : [{
                xtype       : 'textfield',
                fieldLabel  : _('seosuite.tab_seo.label_freeze_uri', {
                    site_url    : MODx.config.site_url
                }),
                name        : 'seosuite_uri',
                anchor      : '100%',
                value       : SeoSuite.record.seosuite_uri,
                enableKeyEvents : true,
                listeners   : {
                    keyup       : {
                        fn          : function(tf) {
                            this.onUpdateFreezeUri(tf, 'keyup');
                        },
                        scope       : this
                    },
                    change      : {
                        fn          : function(tf) {
                            this.onUpdateFreezeUri(tf, 'change');
                        },
                        scope       : this
                    }
                }
            }, {
                xtype       : MODx.expandHelp ? 'label' : 'hidden',
                html        : _('seosuite.tab_seo.label_freeze_uri_desc', {
                    site_url    : MODx.config.site_url
                }),
                cls         : 'desc-under'
            }]
        }, {
            xtype       : 'checkbox',
            boxLabel    : _('seosuite.tab_seo.label_canonical'),
            name        : 'seosuite_canonical',
            anchor      : '100%',
            inputValue  : 1,
            checked     : SeoSuite.record.seosuite_canonical,
            listeners   : {
                check       : {
                    fn          : this.onUpdateCanonical,
                    scope       : this
                },
                afterrender : {
                    fn          : this.onUpdateCanonical,
                    scope       : this
                }
            }
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.tab_seo.label_canonical_desc'),
            cls         : 'desc-under'
        }, {
            id          : 'seosuite-canonical-uri-container',
            layout      : 'form',
            labelSeparator : '',
            items       : [{
                xtype       : 'textfield',
                fieldLabel  : _('seosuite.tab_seo.label_canonical_uri', {
                    site_url    : MODx.config.site_url
                }),
                name        : 'seosuite_canonical_uri',
                anchor      : '100%',
                value       : SeoSuite.record.seosuite_canonical_uri
            }, {
                xtype       : MODx.expandHelp ? 'label' : 'hidden',
                html        : _('seosuite.tab_seo.label_canonical_uri_desc', {
                    site_url    : MODx.config.site_url
                }),
                cls         : 'desc-under'
            }]
        }]
    });

    tabs.push({
        title       : _('seosuite.tab_seo.tab_sitemap'),
        layout      : 'form',
        hideMode: 'offsets',
        labelAlign  : 'top',
        labelSeparator : '',
        items       : [{
            xtype       : 'checkbox',
            hideLabel   : true,
            boxLabel    : _('seosuite.tab_seo.label_sitemap'),
            name        : 'seosuite_sitemap',
            deferredRender : false,
            anchor      : '100%',
            inputValue  : 1,
            checked     : SeoSuite.record.seosuite_sitemap,
            listeners   : {
                check       : {
                    fn          : this.onUpdateSitemap,
                    scope       : this
                },
                afterrender : {
                    fn          : this.onUpdateSitemap,
                    scope       : this
                }
            }
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.tab_seo.label_sitemap_desc'),
            cls         : 'desc-under'
        }, {
            id          : 'seosuite-sitemap-container',
            layout      : 'form',
            labelSeparator : '',
            items       : [{
                layout      : 'column',
                labelAlign  : 'top',
                defaults    : {
                    layout      : 'form',
                    labelSeparator : ''
                },
                items       : [{
                    columnWidth : .5,
                    items       : [{
                        xtype       : 'seosuite-combo-sitemap-prio',
                        fieldLabel  : _('seosuite.tab_seo.label_sitemap_prio'),
                        description : MODx.expandHelp ? '' : _('seosuite.tab_seo.label_sitemap_prio_desc'),
                        name        : 'seosuite_sitemap_prio',
                        anchor      : '100%',
                        value       : SeoSuite.record.seosuite_sitemap_prio
                    }, {
                        xtype       : MODx.expandHelp ? 'label' : 'hidden',
                        html        : _('seosuite.tab_seo.label_sitemap_prio_desc'),
                        cls         : 'desc-under'
                    }]
                }, {
                    columnWidth : .5,
                    items       : [{
                        xtype       : 'seosuite-combo-sitemap-changefreq',
                        fieldLabel  : _('seosuite.tab_seo.label_sitemap_changefreq'),
                        description : MODx.expandHelp ? '' : _('seosuite.tab_seo.label_sitemap_changefreq_desc'),
                        name        : 'seosuite_sitemap_changefreq',
                        anchor      : '100%',
                        value       : SeoSuite.record.seosuite_sitemap_changefreq
                    }, {
                        xtype       : MODx.expandHelp ? 'label' : 'hidden',
                        html        : _('seosuite.tab_seo.label_sitemap_changefreq_desc'),
                        cls         : 'desc-under'
                    }]
                }]
            }]
        }]
    });

    var resource = Ext.getCmp('modx-panel-resource');

    if (resource) {
        if (resource.mode === 'update') {
            tabs.push({
                title       : _('seosuite.tab_seo.tab_urls'),
                layout      : 'form',
                labelAlign  : 'top',
                labelSeparator : '',
                items       : [{
                    xtype       : 'seosuite-grid-redirects',
                    resource    : resource.record.id,
                    mode        : 'resource'
                }]
            });
        }
    }

    Ext.applyIf(config, {
        title       : _('seosuite.tab_seo'),
        items       : [{
            xtype       : 'modx-vtabs',
            deferredRender : false,
            items       : tabs
        }],
        listeners   : {
            afterrender : {
                fn      : this.onAfterRender,
                scope   : this
            }
        }
    });

    SeoSuite.panel.SeoTab.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.panel.SeoTab, Ext.Panel, {
    onAfterRender: function() {
        ['modx-resource-searchable', 'modx-resource-uri-override', 'modx-resource-uri'].forEach(function(key) {
            var field = Ext.getCmp(key);

            if (field) {
                field.hide();
            }
        });
    },
    onToggleFreezeUri: function(tf) {
        var freezeUri = Ext.getCmp('modx-resource-uri-override');

        if (freezeUri) {
            freezeUri.setValue(tf.getValue());
            freezeUri.fireEvent('change', this);
        }

        if (tf.getValue()) {
            Ext.getCmp('seosuite-freeze-uri-container').show();
        } else {
            Ext.getCmp('seosuite-freeze-uri-container').hide();
        }
    },
    onUpdateFreezeUri: function(tf, event) {
        var uri = Ext.getCmp('modx-resource-uri');

        if (uri) {
            uri.setValue(tf.getValue());

            if (event) {
                uri.fireEvent(event, uri);
            }
        }
    },
    onUpdateCanonical: function(tf) {
        if (tf.getValue()) {
            Ext.getCmp('seosuite-canonical-uri-container').show();
        } else {
            Ext.getCmp('seosuite-canonical-uri-container').hide();
        }
    },
    onUpdateSitemap: function(tf) {
        if (tf.getValue()) {
            Ext.getCmp('seosuite-sitemap-container').show();
        } else {
            Ext.getCmp('seosuite-sitemap-container').hide();
        }
    }
});

Ext.reg('seosuite-panel-seo-tab', SeoSuite.panel.SeoTab);
