Ext.onReady(function() {
    console.log(SeoSuite.record);

    var panel = Ext.getCmp('modx-resource-tabs');

    if (panel) {
        ['modx-resource-searchable', 'modx-resource-uri-override', 'modx-resource-uri'].forEach(function(field) {
            var field = Ext.getCmp(field);

            if (field) {
                field.hide();
            }
        });

        panel.add({
            title       : _('seosuite.tab'),
            items       : [{
                xtype       : 'modx-vtabs',
                items       : [{
                    title       : _('seosuite.tab.tab_searchable'),
                    layout      : 'form',
                    labelAlign  : 'top',
                    labelSeparator : '',
                    items       : [{
                        xtype       : 'seosuite-combo-index-type',
                        fieldLabel  : _('seosuite.tab.label_index'),
                        description : MODx.expandHelp ? '' : _('seosuite.tab.label_index_desc'),
                        name        : 'seosuite_index_type',
                        anchor      : '100%',
                        value       : SeoSuite.record.seosuite_index_type || SeoSuite.config['tab_default_index_type']
                    }, {
                        xtype       : MODx.expandHelp ? 'label' : 'hidden',
                        html        : _('seosuite.tab.label_index_desc'),
                        cls         : 'desc-under'
                    }, {
                        xtype       : 'seosuite-combo-follow-type',
                        fieldLabel  : _('seosuite.tab.label_follow'),
                        description : MODx.expandHelp ? '' : _('seosuite.tab.label_follow_desc'),
                        name        : 'seosuite_follow_type',
                        anchor      : '100%',
                        value       : SeoSuite.record.seosuite_follow_type || SeoSuite.config['tab_default_follow_type']
                    }, {
                        xtype       : MODx.expandHelp ? 'label' : 'hidden',
                        html        : _('seosuite.tab.label_follow_desc'),
                        cls         : 'desc-under'
                    }, {
                        xtype       : 'checkbox',
                        boxLabel    : _('seosuite.tab.label_searchable'),
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
                        html        : _('seosuite.tab.label_searchable_desc'),
                        cls         : 'desc-under'
                    }, {
                        xtype       : 'checkbox',
                        boxLabel    : _('seosuite.tab.label_override_uri', {
                            site_url    : MODx.config.site_url
                        }),
                        name        : 'seosuite_override_uri',
                        anchor      : '100%',
                        inputValue  : 1,
                        checked     : SeoSuite.record.seosuite_override_uri,
                        listeners   : {
                            check       : {
                                fn          : function(tf) {
                                    var urioverride = Ext.getCmp('modx-resource-uri-override');

                                    if (urioverride) {
                                        urioverride.setValue(tf.getValue());
                                    }

                                    if (tf.getValue()) {
                                        Ext.getCmp('seosuite-freeze-uri-container').show();
                                    } else {
                                        Ext.getCmp('seosuite-freeze-uri-container').hide();
                                    }
                                },
                                scope       : this
                            }
                        }
                    }, {
                        xtype       : MODx.expandHelp ? 'label' : 'hidden',
                        html        : _('seosuite.tab.label_override_uri_desc', {
                            site_url    : MODx.config.site_url
                        }),
                        cls         : 'desc-under'
                    }, {
                        id          : 'seosuite-freeze-uri-container',
                        layout      : 'form',
                        labelSeparator : '',
                        hidden      : !SeoSuite.record.seosuite_override_uri,
                        items       : [{
                            xtype       : 'textfield',
                            fieldLabel  : _('seosuite.tab.label_freeze_uri', {
                                site_url    : MODx.config.site_url
                            }),
                            name        : 'seosuite_uri',
                            anchor      : '100%',
                            value       : SeoSuite.record.seosuite_uri,
                            listeners   : {
                                change      : {
                                    fn          : function(tf) {
                                        var uri = Ext.getCmp('modx-resource-uri');

                                        if (uri) {
                                            uri.setValue(tf.getValue());
                                        }
                                    },
                                    scope       : this
                                }
                            }
                        }, {
                            xtype       : MODx.expandHelp ? 'label' : 'hidden',
                            html        : _('seosuite.tab.label_freeze_uri_desc', {
                                site_url    : MODx.config.site_url
                            }),
                            cls         : 'desc-under'
                        }]
                    }]
                }, {
                    title       : _('seosuite.tab.tab_sitemap'),
                    layout      : 'form',
                    labelAlign  : 'top',
                    labelSeparator : '',
                    items       : [{
                        xtype       : 'seosuite-combo-sitemap-prio',
                        fieldLabel  : _('seosuite.tab.label_freeze_sitemap_prio'),
                        description : MODx.expandHelp ? '' : _('seosuite.tab.label_freeze_sitemap_prio_desc'),
                        name        : 'seosuite_sitemap_prio',
                        anchor      : '100%',
                        value       : SeoSuite.record.seosuite_sitemap_prio || 'normal'
                    }, {
                        xtype       : MODx.expandHelp ? 'label' : 'hidden',
                        html        : _('seosuite.tab.label_freeze_sitemap_prio_desc'),
                        cls         : 'desc-under'
                    }]
                }, {
                    title       : _('seosuite.tab.tab_redirects')
                }]
            }]
        });
    }
});