SeoSuite.grid.Urls = function(config) {
    config = config || {};

    config.tbar = [{
        text        : '<i class="icon icon-upload"></i>' + _('seosuite.urls_import'),
        cls         : 'primary-button',
        handler     : this.importUrls,
        scope       : this
    }, {
        text        : _('bulk_actions'),
        menu        : [{
            text        : '<i class="x-menu-item-icon icon icon-times"></i>' + _('seosuite.urls_remove'),
            handler     : this.removeSelectedUrls,
            scope       : this
        }]
    }, {
        text        : '<i class="icon icon-eye-slash"></i>' + _('seosuite.exclude_words'),
        handler     : this.excludeWords,
        scope       : this
    }, '->', {
        xtype       : 'textfield',
        name        : 'seosuite-filter-urls-search',
        id          : 'seosuite-filter-urls-search',
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
        id          : 'seosuite-filter-urls-clear',
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
            header      : _('seosuite.label_url_url'),
            dataIndex   : 'url',
            sortable    : true,
            editable    : false,
            width       : 250,
            renderer    : this.renderUrl
        }, {
            header      : _('seosuite.label_url_suggestions'),
            dataIndex   : 'suggestions',
            sortable    : false,
            editable    : false,
            width       : 100,
            fixed       : true,
            renderer    : this.renderSuggestions
        }, {
            header      : _('seosuite.label_url_visits'),
            dataIndex   : 'visits',
            sortable    : false,
            editable    : false,
            width       : 100,
            fixed       : true
        }, {
            header      : _('seosuite.label_url_last_visit'),
            dataIndex   : 'time_ago',
            sortable    : false,
            editable    : false,
            width       : 200,
            fixed       : true
        }, {
            header      : _('seosuite.label_url_createdon'),
            dataIndex   : 'createdon',
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
        id          : 'seosuite-grid-urls',
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/urls/getlist'
        },
        fields      : ['id', 'context_key', 'url', 'suggestions', 'visits', 'last_visit', 'createdon', 'time_ago', 'site_url'],
        paging      : true,
        pageSize    : MODx.config.default_per_page > 30 ? MODx.config.default_per_page : 30,
        refreshGrid : []
    });

    SeoSuite.grid.Urls.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.grid.Urls, MODx.grid.Grid, {
    filterSearch: function(tf, nv, ov) {
        this.getStore().baseParams.query = tf.getValue();

        this.getBottomToolbar().changePage(1);
    },
    clearFilter: function() {
        this.getStore().baseParams.query = '';

        Ext.getCmp('seosuite-filter-urls-search').reset();

        this.getBottomToolbar().changePage(1);
    },
    getMenu: function() {
        return [{
            text    : '<i class="x-menu-item-icon icon icon-edit"></i>' + _('seosuite.redirect_create'),
            handler : this.createUrlRedirect,
            scope   : this
        }, '-', {
            text    : '<i class="x-menu-item-icon icon icon-search"></i>' + _('seosuite.url_suggestions'),
            handler : this.findUrlSuggestions,
            scope   : this
        }, '-', {
            text    : '<i class="x-menu-item-icon icon icon-times"></i>' + _('seosuite.url_remove'),
            handler : this.removeUrl,
            scope   : this
        }];
    },
    refreshGrids: function() {
        if (typeof this.config.refreshGrid === 'string') {
            Ext.getCmp(this.config.refreshGrid).refresh();
        } else {
            this.config.refreshGrid.forEach(function(grid) {
                Ext.getCmp(grid).refresh();
            });
        }
    },
    importUrls: function(btn, e) {
        if (this.importUrlsWindow) {
            this.importUrlsWindow.destroy();
        }

        this.importUrlsWindow = MODx.load({
            xtype       : 'seosuite-window-import-urls',
            closeAction : 'close',
            listeners   : {
                'beforeSubmit': {
                    fn: function() {
                        this.console = MODx.load({
                             xtype         : 'modx-console',
                             register      : 'mgr',
                             topic         : '/seosuiteimport/',
                             show_filename : 0
                        });

                        this.console.show(Ext.getBody());
                    },
                    scope: this
                },
                'success': {
                    fn: function() {
                        this.refresh();
                    },
                    scope: this
                }
            }
        });

        this.importUrlsWindow.show(e.target);
    },
    excludeWords: function(btn, e) {
        if (this.excludeWordsWindow) {
            this.excludeWordsWindow.destroy();
        }

        this.excludeWordsWindow = MODx.load({
            xtype       : 'seosuite-window-exclude-words',
            closeAction : 'close'
        });

        this.excludeWordsWindow.show(e.target);
    },
    createUrlRedirect: function(btn, e) {
        if (this.createUrlRedirectWindow) {
            this.createUrlRedirectWindow.destroy();
        }

        this.createUrlRedirectWindow = MODx.load({
            xtype       : 'seosuite-window-url-create-redirect',
            closeAction : 'close',
            record      : this.menu.record,
            listeners   : {
                'success'   : {
                    fn          : function() {
                        this.refreshGrids();
                        this.refresh();
                    },
                    scope       : this
                }
            }
        });

        this.createUrlRedirectWindow.setValues(this.menu.record);
        this.createUrlRedirectWindow.show(e.target);
    },
    removeUrl: function() {
        MODx.msg.confirm({
            title       : _('seosuite.url_remove'),
            text        : _('seosuite.url_remove_confirm'),
            url         : SeoSuite.config.connector_url,
            params      : {
                action      : 'mgr/urls/remove',
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
    removeSelectedUrls: function(btn, e) {
        MODx.msg.confirm({
            title       : _('seosuite.urls_remove'),
            text        : _('seosuite.urls_remove_confirm'),
            url         : SeoSuite.config.connector_url,
            params      : {
                action      : 'mgr/urls/removemultiple',
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
    findUrlSuggestions: function(btn,e) {
        if (this.urlSuggestionsWindow) {
            this.urlSuggestionsWindow.destroy();
        }

        this.urlSuggestionsWindow = MODx.load({
            xtype       : 'seosuite-window-url-suggestions',
            closeAction : 'close',
            record      : this.menu.record,
            listeners   : {
                'success'   : {
                    fn          : function(record) {
                        MODx.msg.status({
                            title   : _('success'),
                            message : record.a.result.message,
                            delay   : 4
                        });

                        this.refresh();
                    },
                    scope       : this
                }
            }
        });

        this.urlSuggestionsWindow.setValues(this.menu.record);
        this.urlSuggestionsWindow.show(e.target);
    },
    renderUrl: function(d, c, e) {
        if (!Ext.isEmpty(e.json.site_url)) {
            return '<span class="x-grid-span">' + e.json.site_url + '</span>' + d;
        }

        return d;
    },
    renderSuggestions: function(d, c) {
        if (d) {
            var count = Object.keys(d).length;

            if (count >= 1) {
                c.css = 'green';

                return _('yes') + ' (' + count + ')';
            } else {
                c.css = 'red';

                return _('no');
            }
        }

        return '-';
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

Ext.reg('seosuite-grid-urls', SeoSuite.grid.Urls);

SeoSuite.window.ExcludeWords = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        autoHeight  : true,
        title       : _('seosuite.exclude_words'),
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/exclude_words/save'
        },
        fields      : [{
            xtype       : 'textarea',
            fieldLabel  : _('seosuite.label_exclude_words'),
            description : MODx.expandHelp ? '' : _('seosuite.label_exclude_words_desc'),
            name        : 'exclude_words',
            anchor      : '100%',
            value       : SeoSuite.config['exclude_words'].join(', ')
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.label_exclude_words_desc'),
            cls         : 'desc-under'
        }],
        listeners: {
            success: function (response) {
                SeoSuite.config['exclude_words'] = response.a.result.object.exclude_words.split(',');
            }
        }
    });

    SeoSuite.window.ExcludeWords.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.window.ExcludeWords, MODx.Window);

Ext.reg('seosuite-window-exclude-words', SeoSuite.window.ExcludeWords);

SeoSuite.window.UrlCreateRedirect = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        autoHeight  : true,
        title       : _('seosuite.redirect_create'),
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/urls/redirects/create'
        },
        fields      : [{
            xtype       : 'hidden',
            name        : 'id'
        }, {
            xtype       : 'statictextfield',
            fieldLabel  : _('seosuite.label_redirect_old_url'),
            description : MODx.expandHelp ? '' : _('seosuite.label_redirect_old_url_desc'),
            name        : 'url',
            anchor      : '100%'
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.label_redirect_old_url_desc'),
            cls         : 'desc-under'
        }, {
            xtype       : 'seosuite-combo-suggestions',
            fieldLabel  : _('seosuite.label_url_suggestion'),
            description : MODx.expandHelp ? '' : _('seosuite.label_url_suggestion_desc'),
            hiddenName  : 'suggestion',
            anchor      : '100%',
            suggestions : config.record.suggestions,
            listeners   : {
                change      : {
                    fn          : this.onHandleSuggestion,
                    scope       : this
                }
            }
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.label_url_suggestion_desc'),
            cls         : 'desc-under'
        }, {
            xtype       : 'textfield',
            fieldLabel  : _('seosuite.label_redirect_new_url'),
            description : MODx.expandHelp ? '' : _('seosuite.label_redirect_new_url_desc'),
            name        : 'new_url',
            hiddenName  : 'new_url',
            anchor      : '100%',
            id          : 'seosuite-redirect-create-new-url'
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

    SeoSuite.window.UrlCreateRedirect.superclass.constructor.call(this,config);
};

Ext.extend(SeoSuite.window.UrlCreateRedirect, MODx.Window, {
    onHandleSuggestion: function(tf) {
        var newUrl = Ext.getCmp('seosuite-redirect-create-new-url');

        if (newUrl) {
            newUrl.setValue(tf.getValue());
        }
    }
});

Ext.reg('seosuite-window-url-create-redirect', SeoSuite.window.UrlCreateRedirect);

SeoSuite.window.UrlSuggestions = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        autoHeight  : true,
        width       : 500,
        title       : _('seosuite.url_suggesstions'),
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/urls/suggestions/find'
        },
        fields      : [{
            xtype       : 'hidden',
            name        : 'id'
        }, {
            xtype       : 'checkbox',
            hideLabel   : true,
            boxLabel    : _('seosuite.label_url_match_context', {
                domain      : config.record.site_url
            }),
            name        : 'match_context',
            inputValue  : 1,
            checked     : true
        }, {
            xtype       : 'label',
            html        : _('seosuite.label_url_match_context_desc', {
                domain      : config.record.site_url
            }),
            cls         : 'desc-under'
        }, {
            xtype       : 'checkbox',
            hideLabel   : true,
            boxLabel    : _('seosuite.label_url_match_create_redirect'),
            name        : 'create_redirect',
            inputValue  : 1
        }, {
            xtype       : 'label',
            html        : _('seosuite.label_url_match_create_redirect_desc'),
            cls         : 'desc-under'
        }],
        saveBtnText: _('seosuite.find_suggestions'),
    });

    SeoSuite.window.UrlSuggestions.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.window.UrlSuggestions,MODx.Window);

Ext.reg('seosuite-window-url-suggestions', SeoSuite.window.UrlSuggestions);

SeoSuite.window.ImportUrls = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        autoHeight  : true,
        title       : _('seosuite.urls_import'),
        hideUpload  : false,
        fileUpload  : true,
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/url/import',
            register    : 'mgr',
            topic       : '/seosuiteimport/'
        },
        fields      : [{
            html        : '<p>'+_('seosuite.import.instructions', {'path': SeoSuite.config.assets_url + 'files/import-example.xls'})+'</p>',
            style       : 'paddingTop: 20px'
        }, {
            xtype       : 'fileuploadfield',
            fieldLabel  : _('seosuite.label_import_file'),
            description : MODx.expandHelp ? '' : _('seosuite.label_import_file_desc'),
            name        : 'file',
            allowBlank  : false,
            anchor      : '100%',
            buttonText  : _('upload.buttons.choose')
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('tinymce.label_import_file_desc'),
            cls         : 'desc-under'
        }, {
            xtype       : 'checkbox',
            hideLabel   : true,
            boxLabel    : _('seosuite.label_url_match_context', {
                domain      : ''
            }),
            name        : 'match_context',
            inputValue  : 1,
            checked     : true
        }, {
            xtype       : 'label',
            html        : _('seosuite.label_url_match_context_desc', {
                domain      : ''
            }),
            cls         : 'desc-under'
        }]
    });

    SeoSuite.window.ImportUrls.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.window.ImportUrls, MODx.Window);

Ext.reg('seosuite-window-import-urls', SeoSuite.window.ImportUrls);
