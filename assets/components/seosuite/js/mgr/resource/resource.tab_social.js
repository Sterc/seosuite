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
                    anchor      : '100%',
                    value       : SeoSuite.record.seosuite_og_title
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_og_title_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'textarea',
                    fieldLabel  : _('seosuite.tab_social.label_og_description'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_og_description_desc'),
                    name        : 'seosuite_og_description',
                    anchor      : '100%',
                    value       : SeoSuite.record.seosuite_og_description
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_og_description_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'seosuite-combo-social-og-type',
                    fieldLabel  : _('seosuite.tab_social.label_og_type'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_og_type_desc'),
                    name        : 'seosuite_og_type',
                    anchor      : '100%',
                    value       : SeoSuite.record.seosuite_og_type
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_og_type_desc'),
                    cls         : 'desc-under'
                }, {
                    layout      : 'column',
                    labelAlign  : 'top',
                    defaults    : {
                        layout      : 'form',
                        labelSeparator : ''
                    },
                    items       : [{
                        columnWidth : .5,
                        items       : [{
                            xtype       : 'modx-combo-browser',
                            fieldLabel  : _('seosuite.tab_social.label_og_image'),
                            description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_og_image_desc'),
                            name        : 'seosuite_og_image',
                            anchor      : '100%',
                            value       : SeoSuite.record.seosuite_og_image,
                            source      : MODx.config.default_media_source,
                            allowedFileTypes : SeoSuite.config.tab_social.image_types,
                            listeners   : {
                                'change'    : {
                                    fn          : function(tf) {
                                        if (Ext.isEmpty(tf.getValue())) {
                                            this.setImage('', 'seosuite-og-image');
                                        }
                                    },
                                    scope       : this
                                },
                                'select'    : {
                                    fn          : function(tf) {
                                        this.setImage(tf.fullRelativeUrl, 'seosuite-og-image');
                                    },
                                    scope       : this
                                }
                            }
                        }, {
                            xtype       : MODx.expandHelp ? 'label' : 'hidden',
                            html        : _('seosuite.tab_social.label_og_image_desc'),
                            cls         : 'desc-under'
                        }, {
                            xtype       : 'image',
                            id          : 'seosuite-og-image',
                            src         : SeoSuite.record.seosuite_og_image
                        }]
                    }, {
                        columnWidth : .5,
                        items       : [{
                            xtype       : 'textfield',
                            fieldLabel  : _('seosuite.tab_social.label_og_image_alt'),
                            description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_og_image_alt_desc'),
                            name        : 'seosuite_og_image_alt',
                            anchor      : '100%',
                            value       : SeoSuite.record.seosuite_og_image_alt
                        }, {
                            xtype       : MODx.expandHelp ? 'label' : 'hidden',
                            html        : _('seosuite.tab_social.label_og_image_alt_desc'),
                            cls         : 'desc-under'
                        }]
                    }]
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
                    anchor      : '100%',
                    value       : SeoSuite.record.seosuite_twitter_title
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_twitter_title_desc'),
                    cls         : 'desc-under'
                }, {
                    xtype       : 'textarea',
                    fieldLabel  : _('seosuite.tab_social.label_twitter_description'),
                    description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_twitter_description_desc'),
                    name        : 'seosuite_twitter_description',
                    anchor      : '100%',
                    value       : SeoSuite.record.seosuite_twitter_description
                }, {
                    xtype       : MODx.expandHelp ? 'label' : 'hidden',
                    html        : _('seosuite.tab_social.label_twitter_description_desc'),
                    cls         : 'desc-under'
                }, {
                    layout      : 'column',
                    labelAlign  : 'top',
                    defaults    : {
                        layout      : 'form',
                        labelSeparator : ''
                    },
                    items       : [{
                        columnWidth : .5,
                        items       : [{
                            xtype       : 'seosuite-combo-social-twitter-card',
                            fieldLabel  : _('seosuite.tab_social.label_twitter_card'),
                            description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_twitter_card_desc'),
                            name        : 'seosuite_twitter_card',
                            anchor      : '100%',
                            value       : SeoSuite.record.seosuite_twitter_card
                        }, {
                            xtype       : MODx.expandHelp ? 'label' : 'hidden',
                            html        : _('seosuite.tab_social.label_twitter_card_desc'),
                            cls         : 'desc-under'
                        }]
                    }, {
                        columnWidth : .5,
                        items       : [{
                            xtype       : 'textfield',
                            fieldLabel  : _('seosuite.tab_social.label_twitter_creator_id'),
                            description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_twitter_creator_id_desc'),
                            name        : 'seosuite_twitter_creator_id',
                            anchor      : '100%',
                            value       : SeoSuite.record.seosuite_twitter_creator_id
                        }, {
                            xtype       : MODx.expandHelp ? 'label' : 'hidden',
                            html        : _('seosuite.tab_social.label_twitter_creator_id_desc'),
                            cls         : 'desc-under'
                        }]
                    }]
                }, {
                    layout      : 'column',
                    labelAlign  : 'top',
                    defaults    : {
                        layout      : 'form',
                        labelSeparator : ''
                    },
                    items       : [{
                        columnWidth : .5,
                        items       : [{
                            xtype       : 'modx-combo-browser',
                            fieldLabel  : _('seosuite.tab_social.label_twitter_image'),
                            description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_twitter_image_desc'),
                            name        : 'seosuite_twitter_image',
                            anchor      : '100%',
                            value       : SeoSuite.record.seosuite_twitter_image,
                            source      : MODx.config.default_media_source,
                            allowedFileTypes : SeoSuite.config.tab_social.image_types,
                            listeners   : {
                                'change'    : {
                                    fn          : function(tf) {
                                        if (Ext.isEmpty(tf.getValue())) {
                                            this.setImage('', 'seosuite-twitter-image');
                                        }
                                    },
                                    scope       : this
                                },
                                'select'    : {
                                    fn          : function(tf) {
                                        this.setImage(tf.fullRelativeUrl, 'seosuite-twitter-image');
                                    },
                                    scope       : this
                                }
                            }
                        }, {
                            xtype       : MODx.expandHelp ? 'label' : 'hidden',
                            html        : _('seosuite.tab_social.label_twitter_image_desc'),
                            cls         : 'desc-under'
                        }, {
                            xtype       : 'image',
                            id          : 'seosuite-twitter-image',
                            src         : SeoSuite.record.seosuite_twitter_image
                        }]
                    }, {
                        columnWidth : .5,
                        items       : [{
                            xtype       : 'textfield',
                            fieldLabel  : _('seosuite.tab_social.label_twitter_image_alt'),
                            description : MODx.expandHelp ? '' : _('seosuite.tab_social.label_twitter_image_alt_desc'),
                            name        : 'seosuite_twitter_image_alt',
                            anchor      : '100%',
                            value       : SeoSuite.record.seosuite_twitter_image_alt
                        }, {
                            xtype       : MODx.expandHelp ? 'label' : 'hidden',
                            html        : _('seosuite.tab_social.label_twitter_image_alt_desc'),
                            cls         : 'desc-under'
                        }]
                    }]
                }]
            }]
        }]
    });

    SeoSuite.panel.SocialTab.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.panel.SocialTab, Ext.Panel, {
    setImage: function (image, id) {
        var tf = Ext.getCmp(id);

        if (tf) {
            tf.setSrc(image);
        }
    }
});

Ext.reg('seosuite-panel-social-tab', SeoSuite.panel.SocialTab);

Ext.ux.Image = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        src     : Ext.BLANK_IMAGE_URL,
        autoEl  : {
            tag     : 'img',
            width   : config.width || 150,
            height  : config.height || 150,
            src     : this.getSrc(config.src, config.width || 150, config.height || 150),
            cls     : 'x-field-image'
        }
    });

    Ext.ux.Image.superclass.constructor.call(this, config);
};

Ext.extend(Ext.ux.Image, Ext.Component, {
    setSrc: function(src) {
        this.getEl().setVisibilityMode(Ext.Element.DISPLAY);

        var image = this.getSrc(src, this.autoEl.height, this.autoEl.width);

        this.getEl().dom.src = image;

        if (image === Ext.BLANK_IMAGE_URL) {
            this.getEl().hide();
        } else {
            this.getEl().show();
        }
    },
    getSrc: function(src, width, height) {
        if (src) {
            return MODx.config.connectors_url + 'system/phpthumb.php?h=' + height + '&w=' + width + '&zc=1&src=' + src;
        }

        return Ext.BLANK_IMAGE_URL;
    }
});

Ext.reg('image', Ext.ux.Image);