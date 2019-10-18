SeoSuite.grid.Redirects = function(config) {
    config = config || {};

    config.tbar = [{
        text        : _('seosuite.redirect_create'),
        cls         : 'primary-button',
        handler     : this.createRedirect,
        scope       : this
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

    var columns = new Ext.grid.ColumnModel({
        columns     : [{
            header      : _('seosuite.label_redirect_old_url'),
            dataIndex   : 'old_url',
            sortable    : true,
            editable    : false,
            width       : 250
        }, {
            header      : _('seosuite.label_redirect_new_url'),
            dataIndex   : 'new_url',
            sortable    : false,
            editable    : false,
            width       : 350,
            fixed       : true
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
        cm          : columns,
        id          : 'seosuite-grid-redirects',
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/redirects/getlist'
        },
        autosave    : true,
        save_action : 'mgr/redirects/updatefromgrid',
        fields      : ['id', 'resource_id', 'old_url', 'new_url', 'redirect_type', 'active', 'editedon'],
        paging      : true,
        pageSize    : MODx.config.default_per_page > 30 ? MODx.config.default_per_page : 30
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
        
        this.createRedirectWindow = MODx.load({
            xtype       : 'seosuite-window-redirect-create',
            closeAction : 'close',
            listeners   : {
                'success'   : {
                    fn          : this.refresh,
                    scope       : this
                }
            }
        });
        
        this.createRedirectWindow.show(e.target);
    },
    updateRedirect: function(btn, e) {
        if (this.updateRedirectWindow) {
            this.updateRedirectWindow.destroy();
        }
        
        this.updateRedirectWindow = MODx.load({
            xtype       : 'seosuite-window-redirect-update',
            record      : this.menu.record,
            closeAction : 'close',
            listeners   : {
                'success'   : {
                    fn          : this.refresh,
                    scope       : this
                }
            }
        });
        
        this.updateRedirectWindow.setValues(this.menu.record);
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
        width       : 600,
        title       : _('seosuite.redirect_create'),
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/redirects/create'
        },
        fields      : [{
            layout      : 'column',
            defaults    : {
                layout      : 'form',
                labelSeparator : ''
            },
            items       : [{
                columnWidth : .9,
                items       : [{
                    xtype       : 'textfield',
                    fieldLabel  : _('seosuite.label_redirect_old_url'),
                    description : MODx.expandHelp ? '' : _('seosuite.label_redirect_old_url_desc'),
                    name        : 'old_url',
                    anchor      : '100%',
                    allowBlank  : false
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.label_redirect_old_url_desc'),
                    cls         : 'desc-under'
                }]
            }, {
                columnWidth : .1,
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
        width       : 600,
        title       : _('seosuite.redirect_update'),
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/redirects/update'
        },
        fields      : [{
            xtype       : 'hidden',
            name        : 'id'
        }, {
            layout      : 'column',
            defaults    : {
                layout      : 'form',
                labelSeparator : ''
            },
            items       : [{
                columnWidth : .9,
                items       : [{
                    xtype       : 'textfield',
                    fieldLabel  : _('seosuite.label_redirect_old_url'),
                    description : MODx.expandHelp ? '' : _('seosuite.label_redirect_old_url_desc'),
                    name        : 'old_url',
                    anchor      : '100%',
                    allowBlank  : false
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.label_redirect_old_url_desc'),
                    cls         : 'desc-under'
                }]
            }, {
                columnWidth : .1,
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