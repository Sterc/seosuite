/**
 * Loads a grid of 10 most recently added SeoSuite URLs
 */
SeoSuite.grid.RecentUrls = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url        : SeoSuite.config.connector_url,
        baseParams : {
            action  : 'mgr/urls/getlist',
            sortby  : 'createdon',
            sortdir : 'desc',
            limit   : 10
        },
        pageSize   : 10,
        fields     : ['id', 'site_url', 'url', 'visits', 'createdon'],
        autoHeight : true,
        paging     : false,
        remoteSort : true,
        columns: [{
            header    : _('seosuite.label_url_url'),
            dataIndex : 'url',
            width     : 300,
            renderer  : this.renderUrl
        }, {
            header    : _('seosuite.label_url_visits'),
            dataIndex : 'visits',
            width     : 60
        }, {
            header    : _('seosuite.label_url_createdon'),
            dataIndex : 'createdon',
            width     : 160
        }],
        listeners: {
            afterrender: this.onAfterRender
        }
    });

    SeoSuite.grid.RecentUrls.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.grid.RecentUrls, MODx.grid.Grid, {
    windows: {},
    renderUrl: function (value, props, row) {
        var output = '';

        if (row.data.site_url) {
            output += row.data.site_url;
        }

        output += value;

        return output;
    },
    onAfterRender: function () {
        var cnt    = Ext.getCmp('modx-content'),
            parent = Ext.get('seosuite-grid-urls');

        if (cnt && parent) {
            cnt.on('afterlayout', function(elem, layout) {
                var width = parent.getWidth();

                /* Only resize when more than 500px (else let's use/enable the horizontal scrolling). */
                if (width > 500) {
                    this.setWidth(width);
                }
            }, this);
        }
    }
});

Ext.reg('seosuite-dashboard-grid-urls', SeoSuite.grid.RecentUrls);
