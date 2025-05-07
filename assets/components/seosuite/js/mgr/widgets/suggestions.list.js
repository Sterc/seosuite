SeoSuite.grid.SuggestionsList = function(config) {
    config = config || {};
    
    this.exp = new Ext.grid.RowExpander({
        tpl : new Ext.Template(
            '<p style="padding: 5px;"><b>' + _('seosuite.label_redirect_new_url') + ':</b> {uri}</p>'
        )
    });
    
    var sm = new Ext.grid.CheckboxSelectionModel();
    
    Ext.applyIf(config, {
        id          : 'seosuite-grid-suggestions-list',
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Urls\\Suggestions\\GetList',
            limit       : 20
        },
        fields      : ['id', 'url_id', 'url', 'resource_id', 'pagetitle', 'uri', 'score', 'context_key', 'createdon'],
        paging      : true,
        pageSize    : 20,
        remoteSort  : true,
        sm          : sm,
        plugins     : [this.exp],
        columns     : [
            this.exp,
            sm,
            {
                header      : _('id'),
                dataIndex   : 'id',
                width       : 50,
                fixed       : true,
                sortable    : true
            }, {
                header      : _('seosuite.label_url'),
                dataIndex   : 'url',
                width       : 200,
                sortable    : true
            }, {
                header      : _('pagetitle'),
                dataIndex   : 'pagetitle',
                width       : 200,
                sortable    : true
            }, {
                header      : _('seosuite.label_url_score'),
                dataIndex   : 'score',
                width       : 80,
                fixed       : true,
                sortable    : true,
                renderer    : this.renderScore
            }, {
                header      : _('context_key'),
                dataIndex   : 'context_key',
                width       : 80,
                fixed       : true,
                sortable    : true
            }, {
                header      : _('seosuite.label_url_createdon'),
                dataIndex   : 'createdon',
                width       : 150,
                fixed       : true,
                sortable    : true
            }
        ],
        tbar        : [{
            text        : _('seosuite.create_redirects_selected'),
            cls         : 'primary-button',
            handler     : this.createSelectedRedirects,
            scope       : this
        }, {
            text        : _('seosuite.delete_selected'),
            handler     : this.deleteSelected,
            scope       : this
        }, '->', {
            xtype       : 'textfield',
            name        : 'seosuite-filter-suggestions-search',
            id          : 'seosuite-filter-suggestions-search',
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
            id          : 'seosuite-filter-suggestions-clear',
            text        : _('filter_clear'),
            listeners   : {
                'click'     : {
                    fn          : this.clearFilter,
                    scope       : this
                }
            }
        }]
    });
    
    SeoSuite.grid.SuggestionsList.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.grid.SuggestionsList, MODx.grid.Grid, {
    filterSearch: function(tf, nv, ov) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },
    
    clearFilter: function() {
        this.getStore().baseParams.query = '';
        Ext.getCmp('seosuite-filter-suggestions-search').reset();
        this.getBottomToolbar().changePage(1);
    },
    
    renderScore: function(value) {
        var color = 'green';
        
        if (value < 30) {
            color = 'red';
        } else if (value < 70) {
            color = 'orange';
        }
        
        return '<span style="color: ' + color + ';">' + value + '</span>';
    },
    
    getMenu: function() {
        return [{
            text    : '<i class="x-menu-item-icon icon icon-link"></i>' + _('seosuite.create_redirect'),
            handler : this.createRedirect,
            scope   : this
        }, '-', {
            text    : '<i class="x-menu-item-icon icon icon-edit"></i>' + _('seosuite.suggestion_edit'),
            handler : this.editSuggestion,
            scope   : this
        }, '-', {
            text    : '<i class="x-menu-item-icon icon icon-times"></i>' + _('seosuite.suggestion_delete'),
            handler : this.deleteSuggestion,
            scope   : this
        }];
    },
    
    createSelectedRedirects: function() {
        var selected = this.getSelectionModel().getSelections();
        
        if (selected.length === 0) {
            MODx.msg.alert(_('error'), _('seosuite.error_no_suggestions_selected'));
            return;
        }
        
        // Get the URL data for the selected suggestions
        var urlIds = [];
        var urlMap = {};
        
        Ext.each(selected, function(record) {
            var urlId = record.data.url_id;
            if (!urlMap[urlId]) {
                urlIds.push(urlId);
                urlMap[urlId] = [];
            }
            urlMap[urlId].push(record.data.id);
        });
        
        // Get URL data
        MODx.Ajax.request({
            url: SeoSuite.config.connector_url,
            params: {
                action: '\\Sterc\\SeoSuite\\Processors\\Mgr\\Urls\\Get',
                id: urlIds[0] // For now, just handle the first URL's suggestions
            },
            listeners: {
                'success': {
                    fn: function(response) {
                        if (response.object) {
                            this.createRedirects(urlMap[urlIds[0]], response.object);
                        }
                    },
                    scope: this
                }
            }
        });
    },
    
    createRedirect: function() {
        // Get the URL data for this suggestion
        MODx.Ajax.request({
            url: SeoSuite.config.connector_url,
            params: {
                action: '\\Sterc\\SeoSuite\\Processors\\Mgr\\Urls\\Get',
                id: this.menu.record.url_id
            },
            listeners: {
                'success': {
                    fn: function(response) {
                        if (response.object) {
                            this.createRedirects([this.menu.record.id], response.object);
                        }
                    },
                    scope: this
                }
            }
        });
    },
    
    createRedirects: function(ids, urlData) {
        MODx.msg.confirm({
            title       : _('seosuite.create_redirects'),
            text        : _('seosuite.create_redirects_confirm'),
            url         : SeoSuite.config.connector_url,
            params      : {
                action      : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Urls\\Suggestions\\CreateRedirects',
                ids         : ids.join(','),
                url_id      : urlData.id,
                url_string  : urlData.url,
                context_key : urlData.context_key
            },
            listeners   : {
                'success'   : {
                    fn          : function(response) {
                        MODx.msg.status({
                            title   : _('success'),
                            message : response.message,
                            delay   : 4
                        });
                        
                        // Refresh the grid
                        this.refresh();
                        
                        // Refresh the URLs grid
                        Ext.getCmp('seosuite-grid-urls').refresh();
                        
                        // Refresh the redirects grid
                        Ext.getCmp('seosuite-grid-redirects').refresh();
                    },
                    scope       : this
                }
            }
        });
    },
    
    deleteSelected: function() {
        var selected = this.getSelectionModel().getSelections();
        
        if (selected.length === 0) {
            MODx.msg.alert(_('error'), _('seosuite.error_no_suggestions_selected'));
            return;
        }
        
        var ids = [];
        Ext.each(selected, function(record) {
            ids.push(record.data.id);
        });
        
        MODx.msg.confirm({
            title       : _('seosuite.suggestions_delete'),
            text        : _('seosuite.suggestions_delete_confirm'),
            url         : SeoSuite.config.connector_url,
            params      : {
                action      : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Urls\\Suggestions\\RemoveMultiple',
                ids         : ids.join(',')
            },
            listeners   : {
                'success'   : {
                    fn          : function() {
                        this.refresh();
                    },
                    scope       : this
                }
            }
        });
    },
    
    editSuggestion: function() {
        if (!this.editSuggestionWindow) {
            this.editSuggestionWindow = MODx.load({
                xtype       : 'seosuite-window-suggestion-edit',
                record      : this.menu.record,
                listeners   : {
                    'success'   : {
                        fn          : this.refresh,
                        scope       : this
                    }
                }
            });
        } else {
            this.editSuggestionWindow.setValues(this.menu.record);
        }
        
        this.editSuggestionWindow.show();
    },
    
    deleteSuggestion: function() {
        MODx.msg.confirm({
            title       : _('seosuite.suggestion_delete'),
            text        : _('seosuite.suggestion_delete_confirm'),
            url         : SeoSuite.config.connector_url,
            params      : {
                action      : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Urls\\Suggestions\\Remove',
                id          : this.menu.record.id
            },
            listeners   : {
                'success'   : {
                    fn          : function() {
                        this.refresh();
                    },
                    scope       : this
                }
            }
        });
    }
});

Ext.reg('seosuite-grid-suggestions-list', SeoSuite.grid.SuggestionsList);
