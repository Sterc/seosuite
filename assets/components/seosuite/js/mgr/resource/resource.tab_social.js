Ext.onReady(function() {
    var panel = Ext.getCmp('modx-resource-tabs');

    if (panel) {
        panel.add({
            xtype : 'seosuite-panel-social-tab'
        });
    }
});

SeoSuite.panel.SocialTab = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        title       : _('seosuite.tab_social'),
        items       : [{
            xtype       : 'modx-vtabs',
            items       : [{
                title       : '<i class="icon icon-facebook"></i>' + _('seosuite.tab_social.tab_facebook'),
                layout      : 'form',
                labelAlign  : 'top',
                labelSeparator : '',
                items       : [{
                    xtype       : 'textfield',
                    fieldLabel  : _('seosuite.tab_social.label_og_title'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_og_title_desc'),
                    name        : 'seosuite_og_title',
                    anchor      : '100%'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_og_title_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'textarea',
                    fieldLabel  : _('seosuite.tab_social.label_og_description'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_og_description_desc'),
                    name        : 'seosuite_og_description',
                    anchor      : '100%'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_og_description_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'textfield',
                    fieldLabel  : _('seosuite.tab_social.label_og_image'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_og_image_desc'),
                    name        : 'seosuite_og_image',
                    anchor      : '100%'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_og_image_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'textfield',
                    fieldLabel  : _('seosuite.tab_social.label_og_image_alt'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_og_image_alt_desc'),
                    name        : 'seosuite_og_image_desc',
                    anchor      : '100%'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_og_image_alt_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'seosuite-combo-social-og-type',
                    fieldLabel  : _('seosuite.tab_social.label_og_type'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_og_type_desc'),
                    name        : 'seosuite_og_image_desc',
                    anchor      : '100%'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_og_type_desc'),
                    cls         : 'desc-under'
                }]
            }, {
                title       : '<i class="icon icon-twitter"></i>' + _('seosuite.tab_social.tab_twitter'),
                layout      : 'form',
                labelAlign  : 'top',
                labelSeparator : '',
                items       : [{
                    xtype       : 'textfield',
                    fieldLabel  : _('seosuite.tab_social.label_twitter_title'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_twitter_title_desc'),
                    name        : 'seosuite_twitter_title',
                    anchor      : '100%'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_twitter_title_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'textarea',
                    fieldLabel  : _('seosuite.tab_social.label_twitter_description'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_twitter_description_desc'),
                    name        : 'seosuite_twitter_description',
                    anchor      : '100%'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_twitter_description_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'textfield',
                    fieldLabel  : _('seosuite.tab_social.label_twitter_image'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_twitter_image_desc'),
                    name        : 'seosuite_twitter_image',
                    anchor      : '100%'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_twitter_image_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'textfield',
                    fieldLabel  : _('seosuite.tab_social.label_twitter_image_alt'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_twitter_image_alt_desc'),
                    name        : 'seosuite_twitter_image_desc',
                    anchor      : '100%'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_twitter_image_alt_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'seosuite-combo-social-twitter-card',
                    fieldLabel  : _('seosuite.tab_social.label_twitter_card'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_twitter_card_desc'),
                    name        : 'seosuite_twitter_card',
                    anchor      : '100%'
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_twitter_card_desc'),
                    cls         : 'desc-under'
                }]
            }]
        }]
    });

    SeoSuite.panel.SocialTab.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.panel.SocialTab, Ext.Panel);

Ext.reg('seosuite-panel-social-tab', SeoSuite.panel.SocialTab);