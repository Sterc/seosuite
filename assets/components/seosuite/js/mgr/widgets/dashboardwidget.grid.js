/**
 * Loads a grid of 10 most recently added SeoSuite URLs
 */
SeoSuite.grid.RecentUrls = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('recent_docs')
        ,url: SeoSuite.config.connector_url
        ,baseParams: {
            action: 'mgr/url/getlist'
        }
        ,pageSize: 10
        ,fields: ['id','url','solved','redirect_to','redirect_to_text','suggestions_text']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,viewConfig: {
            forceFit: true,
            getRowClass: function (record, index, rowParams, store)
            {
                var clsName = 'seosuite-row';
                if (record.json.solved) {
                    clsName += ' seosuite-solved';
                }
                return clsName;
            }
        }
        ,columns: [{
            header: _('seosuite.url.url')
            ,dataIndex: 'url'
            ,width: 300
        },{
            header: _('seosuite.url.solved')
            ,dataIndex: 'solved'
            ,renderer: this.renderBoolean
            ,width: 60
        },{
            header: _('seosuite.url.redirect_to')
            ,dataIndex: 'redirect_to_text'
            ,width: 160
        },{
            header: _('seosuite.url.suggestions')
            ,dataIndex: 'suggestions_text'
            ,width: 180
        }]
    });
    SeoSuite.grid.RecentUrls.superclass.constructor.call(this,config);
};
Ext.extend(SeoSuite.grid.RecentUrls,MODx.grid.Grid);
Ext.reg('seosuite-dashboard-grid-urls',SeoSuite.grid.RecentUrls);
