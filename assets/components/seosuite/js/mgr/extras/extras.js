SeoSuite.combo.SitemapPrio = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        store       : new Ext.data.ArrayStore({
            mode        : 'local',
            fields      : ['type', 'label', 'index'],
            data        : [
                ['high', _('seosuite.tab_seo.sitemap_prio_high'), '1.0'],
                ['normal', _('seosuite.tab_seo.sitemap_prio_normal'), '0.5'],
                ['low', _('seosuite.tab_seo.sitemap_prio_low'), '0.25']
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

SeoSuite.combo.SitemapChangeFreq = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        store       : new Ext.data.ArrayStore({
            mode        : 'local',
            fields      : ['type', 'label'],
            data        : [
                ['high', _('seosuite.tab_seo.sitemap_changefreq_always')],
                ['normal', _('seosuite.tab_seo.sitemap_changefreq_hourly')],
                ['daily', _('seosuite.tab_seo.sitemap_changefreq_daily')],
                ['weekly', _('seosuite.tab_seo.sitemap_changefreq_weekly')],
                ['monthly', _('seosuite.tab_seo.sitemap_changefreq_monthly')],
                ['yearly', _('seosuite.tab_seo.sitemap_changefreq_yearly')],
                ['never', _('seosuite.tab_seo.sitemap_changefreq_never')]
            ]
        }),
        remoteSort  : ['label', 'asc'],
        hiddenName  : 'seosuite_sitemap_changefreq',
        valueField  : 'type',
        displayField : 'label',
        mode        : 'local'
    });

    SeoSuite.combo.SitemapChangeFreq.superclass.constructor.call(this,config);
};

Ext.extend(SeoSuite.combo.SitemapChangeFreq, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-sitemap-changefreq', SeoSuite.combo.SitemapChangeFreq);

SeoSuite.combo.RedirectType = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        store       : new Ext.data.ArrayStore({
            mode        : 'local',
            fields      : ['type', 'label'],
            data        : [
                ['301', 'HTTP/1.1 301 Moved Permanently'],
                ['302', 'HTTP/1.1 302 Found'],
                ['303', 'HTTP/1.1 303 See Other']
            ]
        }),
        remoteSort  : ['label', 'asc'],
        hiddenName  : 'redirect_type',
        valueField  : 'label',
        displayField : 'label',
        mode        : 'local',
        value       : 'HTTP/1.1 301 Moved Permanently'
    });

    SeoSuite.combo.RedirectType.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.combo.RedirectType, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-redirect-type', SeoSuite.combo.RedirectType);

SeoSuite.combo.Solved = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        store       : new Ext.data.ArrayStore({
            mode        : 'local',
            fields      : ['value', 'label'],
            data        : [
                [1, _('yes')],
                [0, _('no')]
            ]
        }),
        remoteSort  : ['label', 'asc'],
        hiddenName  : 'solved',
        valueField  : 'value',
        displayField : 'label',
        mode        : 'local'
    });

    SeoSuite.combo.Solved.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.combo.Solved, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-solved', SeoSuite.combo.Solved);