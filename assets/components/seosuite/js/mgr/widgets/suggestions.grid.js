SeoSuite.window.ViewAISuggestions = function(config) {
    config = config || {};
    
    Ext.applyIf(config, {
        title       : _('seosuite.urls_view_suggestions'),
        width       : 800,
        height      : 500,
        modal       : true,
        closeAction : 'close',
        layout      : 'fit',
        buttons     : [{
            text    : _('close'),
            scope   : this,
            handler : function() {
                this.close();
            }
        }],
        items       : [{
            xtype       : 'seosuite-grid-suggestions',
            suggestions : config.suggestions || {},
            urlId       : config.urlId || 0,
            urlString   : config.urlString || '',
            contextKey  : config.contextKey || ''
        }]
    });
    
    SeoSuite.window.ViewAISuggestions.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.window.ViewAISuggestions, Ext.Window);

Ext.reg('seosuite-window-view-ai-suggestions', SeoSuite.window.ViewAISuggestions);

SeoSuite.grid.Suggestions = function(config) {
    config = config || {};
    
    this.exp = new Ext.grid.RowExpander({
        tpl : new Ext.Template(
            '<p style="padding: 5px;"><b>' + _('seosuite.label_redirect_new_url') + ':</b> {uri}</p>'
        )
    });
    
    var sm = new Ext.grid.CheckboxSelectionModel();
    
    Ext.applyIf(config, {
        id          : 'seosuite-grid-suggestions',
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Urls\\Suggestions\\GetList',
            suggestions : Ext.encode(config.suggestions),
            url_id      : config.urlId,
            url_string  : config.urlString
        },
        fields      : ['id', 'pagetitle', 'uri', 'score', 'context_key', 'resource_id'],
        paging      : true,
        pageSize    : 1, // Initially show only the top suggestion for performance
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
            }
        ],
        tbar        : [{
            text        : _('seosuite.create_redirects_selected'),
            cls         : 'primary-button',
            handler     : this.createSelectedRedirects,
            scope       : this
        }, {
            text        : _('seosuite.create_redirects_all'),
            handler     : this.createAllRedirects,
            scope       : this
        }, '->', {
            text        : _('seosuite.load_more_suggestions'),
            handler     : this.loadMoreSuggestions,
            scope       : this
        }]
    });
    
    SeoSuite.grid.Suggestions.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.grid.Suggestions, MODx.grid.Grid, {
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
        
        this.createRedirects(this.getSelectedAsList());
    },
    
    createAllRedirects: function() {
        this.createRedirects('all');
    },
    
    loadMoreSuggestions: function() {
        // Update the page size to show more suggestions
        this.getBottomToolbar().pageSize = 30;
        this.getBottomToolbar().changePage(1);
        
        // Update the button to indicate it's been used
        var btn = this.getTopToolbar().items.items.find(function(item) {
            return item.text === _('seosuite.load_more_suggestions');
        });
        
        if (btn) {
            btn.disable();
            btn.setText(_('seosuite.suggestions_loaded'));
        }
    },
    
    createRedirects: function(ids) {
        MODx.msg.confirm({
            title       : _('seosuite.create_redirects'),
            text        : _('seosuite.create_redirects_confirm'),
            url         : SeoSuite.config.connector_url,
            params      : {
                action      : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Urls\\Suggestions\\CreateRedirects',
                ids         : ids,
                url_id      : this.config.urlId,
                url_string  : this.config.urlString,
                context_key : this.config.contextKey
            },
            listeners   : {
                'success'   : {
                    fn          : function(response) {
                        MODx.msg.status({
                            title   : _('success'),
                            message : response.message,
                            delay   : 4
                        });
                        
                        // Close the window after successful creation
                        this.ownerCt.close();
                        
                        // Refresh the URLs grid
                        Ext.getCmp('seosuite-grid-urls').refresh();
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
                    fn          : this.refresh,
                    scope       : this
                }
            }
        });
    }
});

Ext.reg('seosuite-grid-suggestions', SeoSuite.grid.Suggestions);

// Add the edit suggestion window
SeoSuite.window.SuggestionEdit = function(config) {
    config = config || {};
    
    Ext.applyIf(config, {
        title       : _('seosuite.suggestion_edit'),
        width       : 450,
        autoHeight  : true,
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : '\\Sterc\\SeoSuite\\Processors\\Mgr\\Urls\\Suggestions\\Update'
        },
        fields      : [{
            xtype       : 'hidden',
            name        : 'id'
        }, {
            xtype       : 'hidden',
            name        : 'url_id'
        }, {
            xtype       : 'textfield',
            fieldLabel  : _('seosuite.label_suggestion_resource_id'),
            description : MODx.expandHelp ? '' : _('seosuite.label_suggestion_resource_id_desc'),
            name        : 'resource_id',
            anchor      : '100%',
            allowBlank  : false
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.label_suggestion_resource_id_desc'),
            cls         : 'desc-under'
        }, {
            xtype       : 'numberfield',
            fieldLabel  : _('seosuite.label_suggestion_score'),
            description : MODx.expandHelp ? '' : _('seosuite.label_suggestion_score_desc'),
            name        : 'score',
            anchor      : '100%',
            allowBlank  : false,
            minValue    : 0,
            maxValue    : 100
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.label_suggestion_score_desc'),
            cls         : 'desc-under'
        }]
    });
    
    SeoSuite.window.SuggestionEdit.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.window.SuggestionEdit, MODx.Window);

Ext.reg('seosuite-window-suggestion-edit', SeoSuite.window.SuggestionEdit);
