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
            ,id: config.id || 'seosuite-panel-home'
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
                    xtype: 'panel'
                    ,id: 'exclude_words_wrapper'
                    ,cls: 'main-wrapper'
                    ,layout: 'column'
                    ,items: [{
                        xtype: 'label'
                        ,html: '<b>'+_('setting_seosuite.exclude_words')+'</b>'
                        ,columnWidth: 1
                        ,cls: 'text-label text-normal'
                    },{
                        xtype: 'textfield'
                        ,name: 'exclude_words'
                        ,id: 'exclude_words'
                        ,width: 240
                        ,columnWidth: 'auto'
                        ,value: MODx.config['seosuite.exclude_words']
                    }, {
                        xtype: 'button'
                        ,html: _('save')
                        ,cls: 'primary-button'
                        ,listeners: {
                            click: {
                                fn: this.saveExcludeWords, scope: this
                            }
                        }
                    },{
                        xtype: 'label'
                        ,html: _('setting_seosuite.exclude_words_desc')
                        ,columnWidth: 1
                        ,cls: 'desc-under'
                        ,style: 'margin: 0; padding: 5px 0 0 0;'
                    }]
                },{
                    xtype: 'seosuite-grid-urls'
                    ,cls: 'main-wrapper'
                }]
            }]
        }]
    });
    SeoSuite.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(SeoSuite.panel.Home,MODx.Panel,{
    saveExcludeWords: function (btn, e) {
        var words  = Ext.getCmp('exclude_words').getValue();
        Ext.getCmp('exclude_words').disable();
        MODx.Ajax.request({
            url: MODx.config.connector_url
            , params: {
                action: 'system/settings/update'
                , key: 'seosuite.exclude_words'
                , namespace: 'seosuite'
                , area: 'general'
                , value: words
            }
            , listeners: {
                'success': {
                    fn: function (r) {
                        Ext.getCmp('exclude_words').enable();
                    }, scope: this
                }
                ,'failure': {
                    fn: function (r) {
                        Ext.getCmp('exclude_words').enable();
                    }, scope: this
                }
            }
        });
    }
});
Ext.reg('seosuite-panel-home',SeoSuite.panel.Home);
