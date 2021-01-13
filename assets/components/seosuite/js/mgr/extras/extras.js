SeoSuite.combo.SnippetVariable = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url             : SeoSuite.config.connector_url,
        baseParams      : {
            action          : 'mgr/resource/variables/getlist'
        },
        fields          : ['key', 'value'],
        hiddenName      : 'variable',
        valueField      : 'key',
        displayField    : 'value'
    });

    SeoSuite.combo.SnippetVariable.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.combo.SnippetVariable, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-snippet-variable', SeoSuite.combo.SnippetVariable);

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
        value       : SeoSuite.config.default_redirect_type
    });

    SeoSuite.combo.RedirectType.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.combo.RedirectType, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-redirect-type', SeoSuite.combo.RedirectType);

SeoSuite.combo.SocialOgType = function(config) {
    config = config || {};

    var data = [];

    if (SeoSuite.config.tab_social.og_types) {
        SeoSuite.config.tab_social.og_types.forEach(function (index) {
            data.push([index, index]);
        });
    }

    Ext.applyIf(config, {
        store       : new Ext.data.ArrayStore({
            mode        : 'local',
            fields      : ['value', 'label'],
            data        : data
        }),
        hiddenName  : 'seosuite_og_type',
        valueField  : 'value',
        displayField : 'label',
        mode        : 'local',
        value       : SeoSuite.config.tab_social.default_og_type
    });

    SeoSuite.combo.SocialOgType.superclass.constructor.call(this,config);
};

Ext.extend(SeoSuite.combo.SocialOgType, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-social-og-type', SeoSuite.combo.SocialOgType);

SeoSuite.combo.SocialTwitterCard = function(config) {
    config = config || {};

    var data = [];

    if (SeoSuite.config.tab_social.twitter_cards) {
        SeoSuite.config.tab_social.twitter_cards.forEach(function (index) {
            data.push([index, index]);
        });
    }

    Ext.applyIf(config, {
        store       : new Ext.data.ArrayStore({
            mode        : 'local',
            fields      : ['value', 'label'],
            data        : data
        }),
        hiddenName  : 'seosuite_twitter_card',
        valueField  : 'value',
        displayField : 'label',
        mode        : 'local'
    });

    SeoSuite.combo.SocialTwitterCard.superclass.constructor.call(this,config);
};

Ext.extend(SeoSuite.combo.SocialTwitterCard, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-social-twitter-card', SeoSuite.combo.SocialTwitterCard);

SeoSuite.combo.Suggestions = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url         : SeoSuite.config.connector_url,
        baseParams  : {
            action      : 'mgr/urls/suggestions/getlist',
            combo       : true,
            suggestions : Ext.encode(config.suggestions)
        },
        fields      : ['id', 'pagetitle', 'pagetitle_formatted', 'uri', 'site_url', 'boost'],
        hiddenName  : 'suggestion',
        pageSize    : 15,
        valueField  : 'id',
        displayField : 'pagetitle',
        tpl         : '<tpl for=".">' +
            '<div class="x-combo-list-item x-combo-list-item-boost">' +
                '{pagetitle_formatted:htmlEncode} <span class="x-combo-list-item-booster">({boost} ' + _('seosuite.suggestion_boost') + ')</span>' +
                '<span class="x-combo-list-item-block"><span class="x-grid-span">{site_url}</span>{uri}</span>' +
            '</div>' +
        '</tpl>'
    });

    SeoSuite.combo.Suggestions.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.combo.Suggestions, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-suggestions', SeoSuite.combo.Suggestions);

SeoSuite.combo.Contexts = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url          : SeoSuite.config.connector_url,
        baseParams : {
            action  : 'mgr/context/getlist',
            exclude : config.exclude || 'mgr'
        },
        name         : 'context_key',
        hiddenName   : 'context_key',
        displayField : 'name',
        valueField   : 'key',
        fields       : ['key', 'name'],
        pageSize     : 20,
        tpl          : new Ext.XTemplate('<tpl for=".">' +
            '<div class="x-combo-list-item">' +
                '<span style="font-weight: bold">{name:htmlEncode}</span>' +
                '<tpl if="key !== \'\'">' +
                    ' <span style="font-style: italic; font-size: small;">({key:htmlEncode})</span>' +
                '</tpl>' +
                '<tpl if="key === \'\'">' +
                    ' <span style="font-style: italic; font-size: small;">(wildcard)</span>' +
                '</tpl>' +
            '</div>' +
        '</tpl>')
    });

    SeoSuite.combo.Contexts.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.combo.Contexts, MODx.combo.ComboBox);

Ext.reg('seosuite-combo-contexts', SeoSuite.combo.Contexts);
