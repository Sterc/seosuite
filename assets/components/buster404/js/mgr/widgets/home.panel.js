SeoSuite.panel.Home = function(config) {
    config = config || {};
    var seoTabNotice = '';
    if (SeoSuite.config.seoTabNotice) {
        seoTabNotice = '<p><i><small><br>'+SeoSuite.config.seoTabNotice+'</small></i></p>';
    }
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>'+_('seosuite')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,activeTab: 0
            ,hideMode: 'offsets'
            ,items: [{
                title: _('seosuite.url.urls')
                ,layout: 'anchor'
                ,items: [{
                    html: '<p>'+_('seosuite.url.intro_msg')+'</p>'+seoTabNotice
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'seosuite-grid-urls'
                    ,cls: 'main-wrapper'
                }]
            }]
        }]
    });
    SeoSuite.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(SeoSuite.panel.Home,MODx.Panel);
Ext.reg('seosuite-panel-home',SeoSuite.panel.Home);
