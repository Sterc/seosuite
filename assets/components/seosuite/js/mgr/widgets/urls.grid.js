SeoSuite.grid.Urls = function(config) {
    config = config || {};

    config.tbar = [{
        text        : '<i class="icon icon-upload"></i>' + _('seosuite.urls_import'),
        handler     : this.importUrls,
        scope       : this
    }, {
        text        : '<i class="icon icon-times"></i>' + _('seosuite.exclude_words'),
        handler     : this.excludeWords,
        scope       : this
    }, '->', {
        xtype       : 'seosuite-combo-solved',
        name        : 'seosuite-filter-urls-solved',
        id          : 'seosuite-filter-urls-solved',
        emptyText   : _('seosuite.filter_solved'),
        listeners   : {
            'select'    : {
                fn          : this.filterSolved,
                scope       : this
            }
        }
    }, {
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

    var columns = new Ext.grid.ColumnModel({
        columns     : [{
            header      : _('seosuite.label_url_url'),
            dataIndex   : 'url',
            sortable    : true,
            editable    : false,
            width       : 250
        }, {
            header      : _('seosuite.label_url_visits'),
            dataIndex   : 'triggered',
            sortable    : false,
            editable    : false,
            width       : 150,
            fixed       : true
        }, {
            header      : _('seosuite.label_url_solved'),
            dataIndex   : 'solved',
            sortable    : true,
            editable    : false,
            width       : 150,
            fixed       : true,
            renderer    : this.renderBoolean
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
        cm          : columns,
        id          : 'seosuite-grid-urls',
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/urls/getlist'
        },
        fields      : ['id', 'url', 'solved', 'redirect_to', 'redirect_to_text', 'suggestions', 'suggestions_text', 'triggered', 'createdon'],
        paging      : true,
        pageSize    : MODx.config.default_per_page > 30 ? MODx.config.default_per_page : 30
    });

    SeoSuite.grid.Urls.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.grid.Urls, MODx.grid.Grid, {
    filterSolved: function(tf, nv, ov) {
        this.getStore().baseParams.solved = tf.getValue();

        this.getBottomToolbar().changePage(1);
    },
    filterSearch: function(tf, nv, ov) {
        this.getStore().baseParams.query = tf.getValue();

        this.getBottomToolbar().changePage(1);
    },
    clearFilter: function() {
        this.getStore().baseParams.solved = '';
        this.getStore().baseParams.query = '';

        Ext.getCmp('seosuite-filter-urls-solved').reset();
        Ext.getCmp('seosuite-filter-urls-search').reset();

        this.getBottomToolbar().changePage(1);
    },
    getMenu: function() {
        return [{
            text    : '<i class="x-menu-item-icon icon icon-edit"></i>' + _('seosuite.url_update'),
            handler : this.updateUrl,
            scope   : this
        }, {
            text    : '<i class="x-menu-item-icon icon icon-search"></i>' + _('seosuite.url_suggesstions'),
            handler : this.findUrlSuggestions,
            scope   : this
        }, '-', {
            text    : '<i class="x-menu-item-icon icon icon-times"></i>' + _('seosuite.url_remove'),
            handler : this.removeUrl,
            scope   : this
        }];
    },
    importUrls: function(btn, e) {
        if (this.importUrlsWindow) {
            this.importUrlsWindow.destroy();
        }

        this.importUrlsWindow = MODx.load({
                                        xtype: 'seosuite-window-import',
                                        hideUpload: false,
                                        title: _('seosuite.urls_import'),
                                        listeners: {
                                            'beforeSubmit': {fn:function() {
                                                    var topic = '/seosuiteimport/';
                                                    var register = 'mgr';
                                                    this.console = MODx.load({
                                                                                 xtype: 'modx-console',
                                                                                 register: register,
                                                                                 topic: topic,
                                                                                 show_filename: 0
                                                                             });
                                                    this.console.show(Ext.getBody());
                                                },scope:this},
                                            'success': {fn:function(data) {
                                                    this.refresh();
                                                },scope:this}

                                        }
                                    });

        this.importUrlsWindow.show(e.target);
    },
    excludeWords: function(btn, e) {
        if (this.importUrlsWindow) {
            this.importUrlsWindow.destroy();
        }

        this.importUrlsWindow = MODx.load({
            xtype       : 'seosuite-window-exclude-words',
            closeAction : 'close'
        });

        this.importUrlsWindow.show(e.target);
    },
    updateUrl: function(btn,e,isUpdate) {
        if (!this.menu.record || !this.menu.record.id) return false;

        var updateUrl = MODx.load({
            xtype: 'seosuite-window-url'
            ,title: _('seosuite.url.update')
            ,action: 'mgr/url/update'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });
        updateUrl.fp.getForm().reset();
        updateUrl.fp.getForm().setValues(this.menu.record);
        updateUrl.show(e.target);
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
    findUrlSuggestions: function(btn,e) {
        if (!this.menu.record) return false;

        var suggestionsWindow = MODx.load({
            xtype: 'seosuite-window-suggestions'
            ,title: _('seosuite.url.find_suggestions')
            ,action: 'mgr/url/find_suggestions'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:function(r) {
                    var count = 0;
                    if (r.a.result.object.suggestions) {
                        var result = r.a.result.object.suggestions;
                        count = Object.keys(result).length;
                    }
                    if (count == 0) {
                        Ext.Msg.alert(_('seosuite.url.find_suggestions'), _('seosuite.url.notfound_suggestions'));
                    } else if (count == 1) {
                        Ext.Msg.alert(_('seosuite.url.find_suggestions'), _('seosuite.url.found_suggestions'));
                    } else {
                        Ext.Msg.alert(_('seosuite.url.find_suggestions'), _('seosuite.url.found_suggestions_multiple'));
                    }
                    this.refresh();
                }, scope: this }
            }
        });

        suggestionsWindow.fp.getForm().reset();
        suggestionsWindow.fp.getForm().setValues(this.menu.record);
        suggestionsWindow.show(e.target);
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
            anchor      : '100%'
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.label_exclude_words_desc'),
            cls         : 'desc-under'
        }]
    });

    SeoSuite.window.ExcludeWords.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.window.ExcludeWords, MODx.Window);

Ext.reg('seosuite-window-exclude-words', SeoSuite.window.ExcludeWords);

SeoSuite.window.Url = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('seosuite.url.create')
        ,closeAction: 'close'
        ,url: SeoSuite.config.connector_url
        ,action: 'mgr/url/create'
        ,height: 340
        ,width: 600
        ,fields: [{
            xtype: 'textfield'
            ,name: 'id'
            ,hidden: true
        },{
            xtype: 'textfield'
            ,fieldLabel: _('seosuite.url.url')
            ,name: 'url'
            ,anchor: '100%'
        },{
            layout: 'column',
            items: [{
                columnWidth: 0.5,
                layout: 'form',
                items: [{
                    xtype: 'modx-combo'
                    ,id: 'cmb_suggestions'
                    ,fieldLabel: _('seosuite.url.choose_suggestion')
                    ,tpl: '<tpl for="."><div class="x-combo-list-item" >{pagetitle_id} ({context_key})<br><small>{resource_url}</small></div></tpl>'
                    ,name: "cmb_suggestions"
                    ,hiddenName: "cmb_suggestions_value"
                    ,url: SeoSuite.config.connector_url
                    ,fields: [{
                        name: 'id',
                        type: 'string'
                    },{
                        name: 'pagetitle_id',
                        type: 'string'
                    },{
                        name: 'context_key',
                        type: 'string'
                    },{
                        name: 'resource_url',
                        type: 'string'
                    }]
                    ,displayField: 'pagetitle_id'
                    ,baseParams: {
                        action: 'mgr/resource/getlist'
                        ,limit: 20
                        ,sort: 'pagetitle'
                        ,dir: 'asc'
                        ,ids: JSON.stringify(config.record.suggestions)
                    }
                    ,typeAhead: true
                    ,typeAheadDelay: 250
                    ,editable: true
                    ,forceSelection: true
                    ,emptyText: _('resource')
                    ,anchor: '100%'
                    ,allowBlank: true
                    ,paging: true
                    ,pageSize: 20
                    ,listeners: {
                        'select': {
                            fn:this.setRedirectTo,scope: this
                        }
                    }
                }]
            },{
                columnWidth: 0.5,
                layout: 'form',
                items: [{
                    xtype: 'modx-combo'
                    ,id: 'cmb_redirect_to'
                    ,fieldLabel: _('seosuite.url.choose_manually')
                    ,tpl: '<tpl for="."><div class="x-combo-list-item" >{pagetitle_id} ({context_key})<br><small>{resource_url}</small></div></tpl>'
                    ,name: "cmb_redirect_to"
                    ,hiddenName: "redirect_to_value"
                    ,url: SeoSuite.config.connector_url
                    ,fields: [{
                        name: 'id',
                        type: 'string'
                    },{
                        name: 'pagetitle_id',
                        type: 'string'
                    },{
                        name: 'context_key',
                        type: 'string'
                    },{
                        name: 'resource_url',
                        type: 'string'
                    }]
                    ,displayField: 'pagetitle_id'
                    ,baseParams: {
                        action: 'mgr/resource/getlist'
                        ,limit: 20
                        ,sort: 'pagetitle'
                        ,dir: 'asc'
                    }
                    ,typeAhead: true
                    ,typeAheadDelay: 250
                    ,editable: true
                    ,forceSelection: true
                    ,emptyText: _('resource')
                    ,anchor: '100%'
                    ,allowBlank: true
                    ,paging: true
                    ,pageSize: 20
                    ,listeners: {
                        'select': {
                            fn:this.setRedirectTo,scope: this
                        }
                    }
                }]
            }]
        },{
            xtype: 'hidden'
            ,name: 'redirect_to'
            ,id: 'redirect_to_value'
        },{
            xtype: 'label'
            ,text: _('seosuite.url.redirect_to_selected')+': '
            ,cls: 'text-label text-normal first'
        },{
            xtype: 'label'
            ,id: 'redirect_to_text_value'
            ,html: config.record.redirect_to_text
            ,cls: 'text-label'
        }]
    });
    SeoSuite.window.Url.superclass.constructor.call(this,config);

    /* Dirty fix to set the combobox value to empty, when value from request = 0 */
    var cmb_redirect = Ext.getCmp('cmb_redirect_to');
    var redirect_to = cmb_redirect.getValue();
    if (!redirect_to) {
        cmb_redirect.setValue('');
    }
};
Ext.extend(SeoSuite.window.Url,MODx.Window,{
    setRedirectTo: function (tf, nv, ov) {
        var key = tf.getName();
        var redirect_field = Ext.getCmp('redirect_to_value');
        redirect_field.setValue(tf.getValue());
        var redirect_text = Ext.getCmp('redirect_to_text_value');
        redirect_text.setText(tf.getRawValue());
    }
});
Ext.reg('seosuite-window-url',SeoSuite.window.Url);

SeoSuite.window.Import = function(config) {
    config = config || {};
    var fieldWidth = 450;
    this.ident = config.ident || 'site-mecitem'+Ext.id();
    Ext.applyIf(config,{
        id: this.ident,
        autoHeight: true,
        width: fieldWidth+30,
        modal: true,
        closeAction: 'close',
        url: SeoSuite.config.connector_url,
        baseParams: {
            action: 'mgr/url/import',
            register: 'mgr',
            topic: '/seosuiteimport/'
        },
        fileUpload: true,
        fields: [{
            html: '<p>'+_('seosuite.import.instructions')+'</p>',
            style: 'paddingTop: 20px'
        },{
            xtype: 'textfield',
            fieldLabel: _('seosuite.url.file'),
            buttonText: _('seosuite.url.import_choose'),
            name: 'file',
            inputType: 'file'
        },{
            xtype: 'checkbox',
            name: 'match_site_url',
            boxLabel: _('seosuite.match_site_url'),
            inputValue: 1
        },{
            xtype: 'label'
            ,text: _('seosuite.match_site_url_desc')
            ,cls: 'desc-under'
        }]
    });
    SeoSuite.window.Import.superclass.constructor.call(this,config);
};
Ext.extend(SeoSuite.window.Import,MODx.Window);
Ext.reg('seosuite-window-import',SeoSuite.window.Import);

SeoSuite.window.Suggestions = function(config) {
    config = config || {};
    var fieldWidth = 450;
    this.ident = config.ident || 'site-mecitem'+Ext.id();
    Ext.applyIf(config,{
        id: this.ident,
        autoHeight: true,
        width: fieldWidth+30,
        modal: true,
        closeAction: 'close',
        saveBtnText: _('seosuite.url.find_suggestions'),
        cancelBtnText: _('cancel'),
        url: SeoSuite.config.connector_url,
        fields: [{
            xtype: 'textfield'
            ,name: 'id'
            ,hidden: true
        },{
            xtype: 'textfield'
            ,name: 'url'
            ,hidden: true
        },{
            xtype: 'checkbox',
            name: 'match_site_url',
            boxLabel: _('seosuite.match_site_url'),
            inputValue: 1
        },{
            xtype: 'label'
            ,text: _('seosuite.match_site_url_desc')
            ,cls: 'desc-under'
        }]
    });
    SeoSuite.window.Suggestions.superclass.constructor.call(this,config);
};
Ext.extend(SeoSuite.window.Suggestions,MODx.Window);
Ext.reg('seosuite-window-suggestions',SeoSuite.window.Suggestions);

