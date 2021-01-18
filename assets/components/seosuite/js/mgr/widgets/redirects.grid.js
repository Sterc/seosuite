SeoSuite.grid.Redirects = function(config) {
    config = config || {};

    config.tbar = [{
        text        : _('seosuite.redirect_create'),
        cls         : 'primary-button',
        handler     : this.createRedirect,
        scope       : this
    }, {
        text        : _('bulk_actions'),
        menu        : [{
            text        : '<i class="x-menu-item-icon icon icon-times"></i>' + _('seosuite.redirects_remove'),
            handler     : this.removeSelectedRedirects,
            scope       : this
        }]
    }, '->', {
        xtype       : 'textfield',
        name        : 'seosuite-filter-redirects-search',
        id          : 'seosuite-filter-redirects-search',
        emptyText   : _('search') + '...',
        listeners   : {
            'change'    : {
                fn          : this.filterSearch,
                scope       : this
            },
            'render'    : {
                fn          : function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key     : Ext.EventObject.ENTER,
                        fn      : this.blur,
                        scope   : cmp
                    });
                },
                scope       : this
            }
        }
    }, {
        xtype       : 'button',
        cls         : 'x-form-filter-clear',
        id          : 'seosuite-filter-redirects-clear',
        text        : _('filter_clear'),
        listeners   : {
            'click'     : {
                fn          : this.clearFilter,
                scope       : this
            }
        }
    }];

    var sm = new Ext.grid.CheckboxSelectionModel();

    var columns = new Ext.grid.ColumnModel({
        columns     : [sm, {
            header      : _('seosuite.label_redirect_old_url'),
            dataIndex   : 'old_url',
            sortable    : true,
            editable    : false,
            width       : 50,
            renderer    : this.renderOldUrl
        }, {
            header      : _('seosuite.label_redirect_new_url'),
            dataIndex   : 'new_url_formatted',
            sortable    : false,
            editable    : false,
            width       : 50,
            renderer    : this.renderNewUrl,
            hidden      : config.mode === 'resource'
        }, {
            header      : _('seosuite.label_url_visits'),
            dataIndex   : 'visits',
            fixed       : true,
            sortable    : false,
            editable    : false,
            width       : 100
        }, {
            header      : _('seosuite.label_url_last_visit'),
            dataIndex   : 'last_visit',
            sortable    : false,
            editable    : false,
            width       : 20,
            renderer    : this.renderDate
        }, {
            header      : _('seosuite.label_redirect_active'),
            dataIndex   : 'active',
            sortable    : true,
            editable    : true,
            width       : 100,
            fixed       : true,
            renderer    : this.renderBoolean,
            editor      : {
                xtype       : 'modx-combo-boolean'
            }
        }, {
            header      : _('last_modified'),
            dataIndex   : 'editedon',
            sortable    : true,
            editable    : false,
            fixed       : true,
            width       : 200,
            renderer    : this.renderDate
        }]
    });
    
    Ext.applyIf(config, {
        sm          : sm,
        cm          : columns,
        id          : 'seosuite-grid-redirects',
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/redirects/getlist',
            resource    : config.resource
        },
        autosave    : true,
        save_action : 'mgr/redirects/updatefromgrid',
        fields      : ['id', 'context_key', 'resource_id', 'old_url', 'new_url', 'redirect_type', 'visits', 'last_visit', 'active', 'editedon', 'new_url_formatted', 'old_site_url', 'new_site_url'],
        paging      : true,
        pageSize    : MODx.config.default_per_page > 30 ? MODx.config.default_per_page : 30,
        emptyText   : config.mode === 'resource' ? _('seosuite.resource_no_redirects') : _('ext_emptymsg'),
        mode        : 'component'
    });

    SeoSuite.grid.Redirects.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.grid.Redirects, MODx.grid.Grid, {
    filterSearch: function(tf, nv, ov) {
        this.getStore().baseParams.query = tf.getValue();
        
        this.getBottomToolbar().changePage(1);
    },
    clearFilter: function() {
        this.getStore().baseParams.query = '';
        
        Ext.getCmp('seosuite-filter-redirects-search').reset();
        
        this.getBottomToolbar().changePage(1);
    },
    getMenu: function() {
        return [{
            text    : '<i class="x-menu-item-icon icon icon-edit"></i>' + _('seosuite.redirect_update'),
            handler : this.updateRedirect,
            scope   : this
        }, '-', {
            text    : '<i class="x-menu-item-icon icon icon-times"></i>' + _('seosuite.redirect_remove'),
            handler : this.removeRedirect,
            scope   : this
        }];
    },
    createRedirect: function(btn, e) {
        if (this.createRedirectWindow) {
            this.createRedirectWindow.destroy();
        }

        var record = Ext.applyIf({}, {
            resource_id : this.resource,
            new_url     : this.resource
        });

        this.createRedirectWindow = MODx.load({
            xtype       : 'seosuite-window-redirect-create',
            mode        : this.mode,
            record      : record,
            closeAction : 'close',
            listeners   : {
                'success'   : {
                    fn          : this.refresh,
                    scope       : this
                }
            }
        });

        this.createRedirectWindow.setValues(record);
        this.createRedirectWindow.show(e.target);
    },
    updateRedirect: function(btn, e) {
        if (this.updateRedirectWindow) {
            this.updateRedirectWindow.destroy();
        }

        var record = Ext.applyIf(this.menu.record, {
            resource_id : this.resource,
            new_url     : this.resource
        });

        this.updateRedirectWindow = MODx.load({
            xtype       : 'seosuite-window-redirect-update',
            mode        : this.mode,
            record      : record,
            closeAction : 'close',
            listeners   : {
                'success'   : {
                    fn          : this.refresh,
                    scope       : this
                }
            }
        });
        
        this.updateRedirectWindow.setValues(record);
        this.updateRedirectWindow.show(e.target);
    },
    removeRedirect: function() {
        MODx.msg.confirm({
            title       : _('seosuite.redirect_remove'),
            text        : _('seosuite.redirect_remove_confirm'),
            url         : SeoSuite.config.connector_url,
            params      : {
                action      : 'mgr/redirects/remove',
                id          : this.menu.record.id
            },
            listeners   : {
                'success'   : {
                    fn          : this.refresh,
                    scope       : this
                }
            }
        });
    },
    removeSelectedRedirects: function(btn, e) {
        MODx.msg.confirm({
            title       : _('seosuite.redirects_remove'),
            text        : _('seosuite.redirects_remove_confirm'),
            url         : SeoSuite.config.connector_url,
            params      : {
                action      : 'mgr/redirects/removemultiple',
                id          : this.getSelectedAsList()
            },
            listeners   : {
                'success'   : {
                    fn          : this.refresh,
                    scope       : this
                }
            }
        });
    },
    renderOldUrl: function(d, c, e) {
        if (/^(((http|https|ftp):\/\/)|www\.)/.test(d)) {
            return d;
        }

        var url = '*/';

        if (!Ext.isEmpty(e.json.old_site_url)) {
            url = e.json.old_site_url;
        }

        return '<span class="x-grid-span">' + url + '</span>' + d;
    },
    renderNewUrl: function(d, c, e) {
        if (/^(((http|https|ftp):\/\/)|www\.)/.test(d)) {
            return d;
        }

        var url = '*/';

        if (!Ext.isEmpty(e.json.new_site_url)) {
            if (!/^(((http|https|ftp):\/\/)|www\.)/.test(d)) {
                url = e.json.new_site_url;
            }
        }

        return '<span class="x-grid-span">' + url + '</span>' + d;
    },
    renderBoolean: function(d, c) {
        c.css = parseInt(d) === 1 || d ? 'green' : 'red';

        return parseInt(d) === 1 || d ? _('yes') : _('no');
    },
    renderDate: function(a) {
        if (Ext.isEmpty(a)) {
            return '—';
        }
        
        return a;
    }
});

Ext.reg('seosuite-grid-redirects', SeoSuite.grid.Redirects);

SeoSuite.window.CreateRedirect = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        autoHeight  : true,
        title       : _('seosuite.redirect_create'),
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/redirects/create'
        },
        fields      : [{
            xtype       : 'hidden',
            name        : 'resource_id'
        }, {
            layout      : 'column',
            defaults    : {
                layout          : 'form',
                labelSeparator  : ''
            },
            items       : [{
                columnWidth : .85,
                defaults    : {
                    msgTarget       : 'under'
                },
                items       : [{
                    xtype       : 'textfield',
                    fieldLabel  : _('seosuite.label_redirect_old_url'),
                    description : MODx.expandHelp ? '' : _('seosuite.label_redirect_old_url_desc'),
                    name        : 'old_url',
                    anchor      : '100%',
                    allowBlank  : false,
                    msgTarget   : 'under'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.label_redirect_old_url_desc'),
                    cls         : 'desc-under'
                }]
            }, {
                columnWidth : .15,
                items       : [{
                    xtype       : 'checkbox',
                    fieldLabel  : _('seosuite.label_redirect_active'),
                    description : MODx.expandHelp ? '' : _('seosuite.label_redirect_active_desc'),
                    name        : 'active',
                    inputValue  : 1,
                    checked     : true
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.label_redirect_active_desc'),
                    cls         : 'desc-under'
                }]
            }]
        }, {
            layout          : 'form',
            labelSeparator  : '',
            hidden          : config.mode === 'resource',
            defaults        : {
                msgTarget       : 'under'
            },
            items       : [{
                xtype       : 'textfield',
                fieldLabel  : _('seosuite.label_redirect_new_url'),
                description : MODx.expandHelp ? '' : _('seosuite.label_redirect_new_url_desc'),
                name        : 'new_url',
                anchor      : '100%',
                allowBlank  : false
            }, {
                xtype       : MODx.expandHelp ? 'label' : 'hidden',
                html        : _('seosuite.label_redirect_new_url_desc'),
                cls         : 'desc-under'
            }]
        }, {
            xtype       : 'seosuite-combo-contexts',
            fieldLabel  : _('seosuite.label_redirect_match_context'),
            description : MODx.expandHelp ? '' : _('seosuite.label_redirect_match_context_desc'),
            name        : 'context_key',
            hidden      : config.mode === 'resource',
            value       : config.mode === 'resource' ? MODx.ctx : '',
            anchor      : '100%',
            allowBlank  : true
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.label_redirect_match_context_desc'),
            hidden      : config.mode === 'resource',
            cls         : 'desc-under'
        }, {
            xtype       : 'seosuite-combo-redirect-type',
            fieldLabel  : _('seosuite.label_redirect_type'),
            description : MODx.expandHelp ? '' : _('seosuite.label_redirect_type_desc'),
            name        : 'redirect_type',
            anchor      : '100%',
            allowBlank  : false
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.label_redirect_type_desc'),
            cls         : 'desc-under'
        }]
    });

    SeoSuite.window.CreateRedirect.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.window.CreateRedirect, MODx.Window);

Ext.reg('seosuite-window-redirect-create', SeoSuite.window.CreateRedirect);

SeoSuite.window.UpdateRedirect = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        autoHeight  : true,
        title       : _('seosuite.redirect_update'),
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/redirects/update'
        },
        fields      : [{
            xtype       : 'hidden',
            name        : 'id'
        }, {
            xtype       : 'hidden',
            name        : 'resource_id'
        }, {
            layout      : 'column',
            defaults    : {
                layout      : 'form',
                labelSeparator : ''
            },
            items       : [{
                columnWidth : .85,
                items       : [{
                    xtype       : 'textfield',
                    fieldLabel  : _('seosuite.label_redirect_old_url'),
                    description : MODx.expandHelp ? '' : _('seosuite.label_redirect_old_url_desc'),
                    name        : 'old_url',
                    anchor      : '100%',
                    allowBlank  : false,
                    msgTarget   : 'under'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.label_redirect_old_url_desc'),
                    cls         : 'desc-under'
                }]
            }, {
                columnWidth : .15,
                items       : [{
                    xtype       : 'checkbox',
                    fieldLabel  : _('seosuite.label_redirect_active'),
                    description : MODx.expandHelp ? '' : _('seosuite.label_redirect_active_desc'),
                    name        : 'active',
                    inputValue  : 1,
                    checked     : true
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.label_redirect_active_desc'),
                    cls         : 'desc-under'
                }]
            }]
        }, {
            layout      : 'form',
            labelSeparator : '',
            hidden      : config.mode === 'resource',
            items       : [{
                xtype       : 'textfield',
                fieldLabel  : _('seosuite.label_redirect_new_url'),
                description : MODx.expandHelp ? '' : _('seosuite.label_redirect_new_url_desc'),
                name        : 'new_url',
                anchor      : '100%',
                allowBlank  : false
            }, {
                xtype       : MODx.expandHelp ? 'label' : 'hidden',
                html        : _('seosuite.label_redirect_new_url_desc'),
                cls         : 'desc-under'
            }]
        }, {
            xtype       : 'seosuite-combo-contexts',
            fieldLabel  : _('seosuite.label_redirect_match_context'),
            description : MODx.expandHelp ? '' : _('seosuite.label_redirect_match_context_desc'),
            name        : 'context_key',
            anchor      : '100%',
            allowBlank  : true
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.label_redirect_match_context_desc'),
            cls         : 'desc-under'
        }, {
            xtype       : 'seosuite-combo-redirect-type',
            fieldLabel  : _('seosuite.label_redirect_type'),
            description : MODx.expandHelp ? '' : _('seosuite.label_redirect_type_desc'),
            name        : 'redirect_type',
            anchor      : '100%',
            allowBlank  : false
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.label_redirect_type_desc'),
            cls         : 'desc-under'
        }]
    });

    SeoSuite.window.UpdateRedirect.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.window.UpdateRedirect, MODx.Window);

Ext.reg('seosuite-window-redirect-update', SeoSuite.window.UpdateRedirect);