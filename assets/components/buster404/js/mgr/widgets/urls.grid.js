Buster404.grid.Urls = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'buster404-grid-urls'
        ,url: Buster404.config.connectorUrl
        ,baseParams: {
            action: 'mgr/url/getlist'
        }
        ,save_action: 'mgr/url/updatefromgrid'
        ,autosave: true
        ,fields: ['id','url','solved','redirect_to','suggestions']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,width: 70
        },{
            header: _('buster404.url')
            ,dataIndex: 'url'
            ,width: 200
       }]
        ,tbar: [{
            xtype: 'button',
            text: _('buster404.url.import'),
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
                            return;
                        },scope:this}
                    }
                });

                this.importUrls.fp.getForm().reset();
                this.importUrls.show(e.target);
            },
            cls: 'container'
        },'->',{
            xtype: 'textfield'
            ,emptyText: _('buster404.global.search') + '...'
            ,listeners: {
                'change': {fn:this.search,scope:this}
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

    ,search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
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
            ,fieldLabel: _('buster404.url')
            ,name: 'url'
            ,anchor: '100%'
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

