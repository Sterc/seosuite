Ext.onReady(function() {
    MODx.load({ xtype: 'seosuite-page-home'});
});

SeoSuite.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'seosuite-panel-home'
            ,renderTo: 'seosuite-panel-home-div'
        }]
    });
    SeoSuite.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(SeoSuite.page.Home,MODx.Component);
Ext.reg('seosuite-page-home',SeoSuite.page.Home);