SeoSuite.combo.IndexType = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        store       : new Ext.data.ArrayStore({
            mode        : 'local',
            fields      : ['type', 'label', 'index'],
            data        : [
                [1, _('yes'), _('seosuite.index_type_index')],
                [0, _('no'), _('seosuite.index_type_noindex')]
            ]
        }),
        remoteSort  : ['label', 'asc'],
        hiddenName  : 'seosuite_index_type',
        valueField  : 'type',
        displayField : 'label',
        mode        : 'local',
        tpl         : new Ext.XTemplate('<tpl for=".">' +
            '<div class="x-combo-list-item">' +
                '{label:htmlEncode} <em>({index:htmlEncode})</em>' +
            '</div>' +
        '</tpl>')
    });

    SeoSuite.combo.IndexType.superclass.constructor.call(this,config);
};

Ext.extend(SeoSuite.combo.IndexType, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-index-type', SeoSuite.combo.IndexType);

SeoSuite.combo.FollowType = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        store       : new Ext.data.ArrayStore({
            mode        : 'local',
            fields      : ['type', 'label', 'index'],
            data        : [
                [1, _('yes'), _('seosuite.follow_type_follow')],
                [0, _('no'), _('seosuite.follow_type_nofollow')]
            ]
        }),
        remoteSort  : ['label', 'asc'],
        hiddenName  : 'seosuite_follow_type',
        valueField  : 'type',
        displayField : 'label',
        mode        : 'local',
        tpl         : new Ext.XTemplate('<tpl for=".">' +
            '<div class="x-combo-list-item">' +
                '{label:htmlEncode} <em>({index:htmlEncode})</em>' +
            '</div>' +
        '</tpl>')
    });

    SeoSuite.combo.FollowType.superclass.constructor.call(this,config);
};

Ext.extend(SeoSuite.combo.FollowType, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-follow-type', SeoSuite.combo.FollowType);

SeoSuite.combo.SitemapPrio = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        store       : new Ext.data.ArrayStore({
            mode        : 'local',
            fields      : ['type', 'label', 'index'],
            data        : [
                ['high', _('seosuite.sitemap_prio_high'), '1.0'],
                ['normal', _('seosuite.sitemap_prio_normal'), '0.5'],
                ['low', _('seosuite.sitemap_prio_low'), '0.25']
            ]
        }),
        remoteSort  : ['label', 'asc'],
        hiddenName  : 'seosuite_sitemap_prio',
        valueField  : 'type',
        displayField : 'label',
        mode        : 'local',
        tpl         : new Ext.XTemplate('<tpl for=".">' +
            '<div class="x-combo-list-item">' +
                '{label:htmlEncode} <em>({index:htmlEncode})</em>' +
            '</div>' +
        '</tpl>')
    });

    SeoSuite.combo.SitemapPrio.superclass.constructor.call(this,config);
};

Ext.extend(SeoSuite.combo.SitemapPrio, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-sitemap-prio', SeoSuite.combo.SitemapPrio);