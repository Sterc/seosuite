/**
 * Loads a grid of 10 most recently added SeoSuite URLs
 */
SeoSuite.grid.RecentUrls = function(config) {
    console.log(_('seosuite.url.url'));
    config = config || {};
    Ext.applyIf(config,{
        title: _('recent_docs')
        ,url: SeoSuite.config.connector_url
        ,baseParams: {
            action: 'mgr/url/getlist'
            ,limit: 10
        }
        ,pageSize: 10
        ,fields: ['id','url','solved','redirect_to','redirect_to_text']
        ,autoHeight: true
        ,paging: false
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
        }]
        ,listeners: {
            afterrender: this.onAfterRender
        }
    });
    SeoSuite.grid.RecentUrls.superclass.constructor.call(this,config);
};
Ext.extend(SeoSuite.grid.RecentUrls,MODx.grid.Grid,{
    windows: {}
    ,renderBoolean: function (value, props, row) {
        return value
            ? String.format('<span class="green"><i class="icon icon-check"></i>&nbsp;&nbsp;{0}</span>', _('yes'))
            : String.format('<span class="red"><i class="icon icon-ban"></i>&nbsp;&nbsp;{0}</span>', _('no'));
    }
    ,onAfterRender: function() {
        var cnt = Ext.getCmp('modx-content')
            // Dashboard widget "parent" (renderTo)
            ,parent = Ext.get('seosuite-grid-urls');

        if (cnt && parent) {
            cnt.on('afterlayout', function(elem, layout) {
                var width = parent.getWidth();
                // Only resize when more than 500px (else let's use/enable the horizontal scrolling)
                if (width > 500) {
                    this.setWidth(width);
                }
            }, this);
        }
    }
});
Ext.reg('seosuite-dashboard-grid-urls',SeoSuite.grid.RecentUrls);
