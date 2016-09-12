Ext.onReady(function() {
    MODx.load({ xtype: 'buster404-page-home'});
});

Buster404.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'buster404-panel-home'
            ,renderTo: 'buster404-panel-home-div'
        }]
    });
    Buster404.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Buster404.page.Home,MODx.Component);
Ext.reg('buster404-page-home',Buster404.page.Home);