Buster404.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>'+_('buster404')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,activeTab: 0
            ,hideMode: 'offsets'
            ,items: [{
                title: _('buster404.url.urls')
                ,items: [{
                    html: '<p>'+_('buster404.url.intro_msg')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'buster404-grid-urls'
                    ,preventRender: true
                    ,cls: 'main-wrapper'
                }]
            }]
        }]
    });
    Buster404.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Buster404.panel.Home,MODx.Panel);
Ext.reg('buster404-panel-home',Buster404.panel.Home);
