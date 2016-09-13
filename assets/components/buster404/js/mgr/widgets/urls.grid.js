Buster404.grid.Urls = function(config) {
    config = config || {};
    if (!config.id) {
        config.id = 'buster404-grid-urls';
    }
    Ext.applyIf(config,{
        id: config.id
        ,url: Buster404.config.connectorUrl
        ,baseParams: {
            action: 'mgr/url/getlist'
        }
        ,fields: ['id','url','solved','redirect_to_text','suggestions_text']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,viewConfig: {
            forceFit: true,
            getRowClass: function (record, index, rowParams, store)
            {
                var clsName = 'buster404-row';
                if (record.json.solved) {
                    clsName += ' buster404-solved';
                }
                return clsName;
            }
        }
        ,columns: [{
            header: _('buster404.url.url')
            ,dataIndex: 'url'
            ,width: 320
        },{
            header: _('buster404.url.solved')
            ,dataIndex: 'solved'
            ,renderer: this.renderBoolean
            ,width: 40
        },{
            header: _('buster404.url.redirect_to')
            ,dataIndex: 'redirect_to_text'
            ,width: 160
        },{
            header: _('buster404.url.suggestions')
            ,dataIndex: 'suggestions_text'
            ,width: 180
        }]
        ,tbar: [{
            xtype: 'button',
            text: '<i class="icon icon-upload"></i>&nbsp;&nbsp;' + _('buster404.url.import'),
            handler: function(btn, e){
                this.importUrls = MODx.load({
                    xtype: 'buster404-window-import',
                    hideUpload: false,
                    title: _('buster404.url.import'),
                    listeners: {
                        'beforeSubmit': {fn:function() {
                            var topic = '/buster404import/';
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

                this.importUrls.fp.getForm().reset();
                this.importUrls.show(e.target);
            },
            cls: 'container'
        },'->',{
            xtype: 'modx-combo'
            ,width: 130
            ,name: "solved"
            ,id: config.id + '-solved-field'
            ,hiddenName: "solved"
            ,store: new Ext.data.SimpleStore({
                data: [
                    ['1', 'Yes'],
                    ['0', 'No']
                ],
                id: 0,
                fields: ["value", "text"]
            })
            ,valueField: "value"
            ,displayField: "text"
            ,forceSelection: true
            ,triggerAction: "all"
            ,editable: true
            ,mode: "local"
            ,emptyText: _('buster404.url.solved')
            ,listeners: {
                'select': {
                    fn:this.filter,scope: this
                }
            }
        },{
            xtype: 'textfield'
            ,width: 180
            ,name: "query"
            ,id: config.id + '-search-field'
            ,emptyText: _('search') + '...'
            ,listeners: {
                'change': {fn:this.filter,scope:this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this);
                            this.blur();
                            return true;
                        }
                        ,scope: cmp
                    });
                },scope:this}
            }
        },{
            xtype: 'button',
            id: config.id + '-search-clear',
            text: '<i class="icon icon-times"></i>',
            listeners: {
                click: {
                    fn: this.clearFilter, scope: this
                }
            }  
        }]
    });
    Buster404.grid.Urls.superclass.constructor.call(this,config);
};
Ext.extend(Buster404.grid.Urls,MODx.grid.Grid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        m.push({
            text: _('buster404.url.update')
            ,handler: this.updateUrl
        });
        m.push({
            text: _('buster404.url.find_suggestions')
            ,handler: this.findSuggestions
        });
        m.push('-');
        m.push({
            text: _('buster404.url.remove')
            ,handler: this.removeUrl
        });
        this.addContextMenuItem(m);
    }

    ,updateUrl: function(btn,e,isUpdate) {
        if (!this.menu.record || !this.menu.record.id) return false;

        var updateUrl = MODx.load({
            xtype: 'buster404-window-url'
            ,title: _('buster404.url.update')
            ,action: 'mgr/url/update'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        updateUrl.fp.getForm().reset();
        updateUrl.fp.getForm().setValues(this.menu.record);
        updateUrl.show(e.target);
    }
    
    ,removeUrl: function(btn,e) {
        if (!this.menu.record) return false;
        
        MODx.msg.confirm({
            title: _('buster404.url.remove')
            ,text: _('buster404.url.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/url/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
    }

    ,findSuggestions: function(btn,e) {
        if (!this.menu.record) return false;
        
        var updateUrl = MODx.load({
            xtype: 'buster404-window-url'
            ,title: _('buster404.url.update')
            ,action: 'mgr/url/update'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        updateUrl.fp.getForm().reset();
        updateUrl.fp.getForm().setValues(this.menu.record);
        updateUrl.show(e.target);
    }

    ,filter: function (tf, nv, ov) {
        var store = this.getStore();
        var key = tf.getName();
        var value = tf.getValue();
        store.baseParams[key] = value;
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }

    ,clearFilter: function (btn, e) {
        var baseParams = this.getStore().baseParams;
        delete baseParams.query;
        delete baseParams.solved;
        this.getStore().baseParams = baseParams;
        Ext.getCmp(this.config.id + '-search-field').setValue('');
        Ext.getCmp(this.config.id + '-solved-field').setValue('');
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }

    ,renderBoolean: function (value, props, row) {
        return value
            ? String.format('<span class="green"><i class="icon icon-check"></i>&nbsp;&nbsp;{0}</span>', _('yes'))
            : String.format('<span class="red"><i class="icon icon-ban"></i>&nbsp;&nbsp;{0}</span>', _('no'));
    }
    
});
Ext.reg('buster404-grid-urls',Buster404.grid.Urls);

Buster404.window.Url = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('buster404.url.create')
        ,closeAction: 'close'
        ,url: Buster404.config.connectorUrl
        ,action: 'mgr/url/create'
        ,fields: [{
            xtype: 'textfield'
            ,name: 'id'
            ,hidden: true
        },{
            xtype: 'textfield'
            ,fieldLabel: _('buster404.url.url')
            ,name: 'url'
            ,anchor: '100%'
        },{
            xtype: 'modx-combo'
            ,fieldLabel: _('buster404.url.redirect_to')
            ,name: "redirect_to"
            ,hiddenName: "redirect_to"
            ,url: Buster404.config.connectorUrl
            ,fields: ['id', 'pagetitle']
            ,displayField: 'pagetitle'
            ,baseParams: {
                action: 'mgr/resource/getlist'
                ,limit: 20
                ,sort: 'pagetitle'
                ,dir: 'asc'
            }
            ,emptyText: _('resource')
            ,anchor: '100%'
            ,allowBlank: false
            ,paging: true
            ,pageSize: 20
        }]
    });
    Buster404.window.Url.superclass.constructor.call(this,config);
};
Ext.extend(Buster404.window.Url,MODx.Window);
Ext.reg('buster404-window-url',Buster404.window.Url);

Buster404.window.Import = function(config) {
    config = config || {};
    var fieldWidth = 450;
    this.ident = config.ident || 'site-mecitem'+Ext.id();
    Ext.applyIf(config,{
        id: this.ident,
        autoHeight: true,
        width: fieldWidth+30,
        modal: true,
        closeAction: 'close',
        url: Buster404.config.connector_url,
        baseParams: {
            action: 'mgr/url/import',
            register: 'mgr',
            topic: '/buster404import/'
        },
        fileUpload: true,
        fields: [{
            xtype: 'textfield',
            fieldLabel: 'File',
            buttonText: _('buster404.url.import_choose'),
            name: 'file',
            inputType: 'file'
        }]
    });
    Buster404.window.Import.superclass.constructor.call(this,config);
};
Ext.extend(Buster404.window.Import,MODx.Window);
Ext.reg('buster404-window-import',Buster404.window.Import);

