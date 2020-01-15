Ext.extend(SeoSuite, Ext.Component, {
    initialize: function() {
        SeoSuite.config.loaded = true;
        SeoSuite.addPanel();

        var longtitleField = Ext.getCmp('seosuite-longtitle');

        ['modx-resource-pagetitle', 'modx-resource-introtext', 'modx-resource-alias', 'modx-resource-uri', 'seosuite-longtitle', 'seosuite-description', 'modx-resource-uri-override'].forEach((function(key) {
            var field = Ext.getCmp(key);

            if (field) {
                if (field.xtype === 'xcheckbox') {
                    field.on('check', this.onRenderPreview, this);
                } else {
                    field.on('keyup', (function(tf) {
                        //if (tf.name === 'pagetitle') {
                            //console.log(tf.name, tf.getValue());
                            //console.log(longtitleField.getValue());

                            //if (longtitleField) {
                            //    if (longtitleField.getValue() === '') {
                            //        longtitleField.emptyText = tf.getValue();
                            //        longtitleField.applyEmptyText();
                            //    }
                            //}

                            //this.count('longtitle');
                        //}

                        this.onRenderPreview();
                    }).bind(this));
                }
            }
        }).bind(this));

        Ext.each(SeoSuite.record.fields.split(','), function(field) {
            SeoSuite.addCounter(field);
        });

        ['modx-resource-longtitle', 'modx-resource-description'].forEach(function(key) {
            var field = Ext.getCmp(key);

            if (field) {
                field.hide();
            }
        });
    },
    addPanel: function() {
        var fp = Ext.getCmp('modx-panel-resource');

        console.log(SeoSuite.record);
        console.log(SeoSuite.config.meta);

        fp.insert(2, {
            xtype        : 'panel',
            anchor       : '100%',
            border       : false,
            layout       : 'form',
            bodyCssClass : 'main-wrapper',
            id           : 'resource-seosuite-panel',
            autoHeight   : true,
            collapsible  : true,
            animCollapse : false,
            hideMode     : 'offsets',
            title        : _('seosuite.tab_meta.seo'),
            items        : [{
                layout      : 'column',
                defaults    : {
                    layout          : 'form',
                    labelAlign      : 'top',
                    labelSeparator  : ''
                },
                items   : [{
                    columnWidth : .5,
                    items       : [{
                        xtype       : 'textfield',
                        fieldLabel  : _('seosuite.tab_meta.focuskeywords'),
                        description : MODx.expandHelp ? '' : _('seosuite.tab_meta.focuskeywords_desc'),
                        anchor      : '100%',
                        name        : 'seosuite_keywords',
                        id          : 'seosuite-keywords',
                        value       : SeoSuite.record.keywords,
                        enableKeyEvents : true,
                        listeners   : {
                            keyup       : function () {
                                MODx.fireResourceFormChange();

                                Ext.each(SeoSuite.record.fields.split(','), function (field) {
                                    var Field = Ext.getCmp('modx-resource-' + field);
                                    if (Field) {
                                        SeoSuite.count(field);
                                    }
                                });
                            }
                        }
                    }, {
                        xtype       : MODx.expandHelp ? 'label' : 'hidden',
                        html        : _('seosuite.tab_meta.focuskeywords_desc'),
                        cls         : 'desc-under'
                    }, {
                        xtype       : 'textfield',
                        fieldLabel  : _('seosuite.tab_meta.longtitle'),
                        description : MODx.expandHelp ? '' : _('seosuite.tab_meta.longtitle_desc'),
                        anchor      : '100%',
                        name        : 'seosuite_longtitle',
                        id          : 'seosuite-longtitle',
                        value       : MODx.activePage.record.longtitle,
                        enableKeyEvents : true,
                        listeners   : {
                            keyup       : {
                                fn          : function (tf) {
                                    Ext.getCmp('modx-resource-longtitle').setValue(tf.getValue());
                                },
                                scope       : this
                            }
                        }
                    }, {
                        xtype       : MODx.expandHelp ? 'label' : 'hidden',
                        html        : _('seosuite.tab_meta.longtitle_desc'),
                        cls         : 'desc-under'
                    }, {
                        xtype       : 'textarea',
                        fieldLabel  :_('seosuite.tab_meta.description'),
                        description : MODx.expandHelp ? '' : _('seosuite.tab_meta.description_desc'),
                        anchor      : '100%',
                        name        : 'seosuite_description',
                        id          : 'seosuite-description',
                        value       : MODx.activePage.record.description,
                        enableKeyEvents : true,
                        listeners   : {
                            keyup       : {
                                fn          : function (tf) {
                                    Ext.getCmp('modx-resource-description').setValue(tf.getValue());
                                },
                                scope       : this
                            }
                        }
                    }, {
                        xtype       : MODx.expandHelp ? 'label' : 'hidden',
                        html        : _('seosuite.tab_meta.description_desc'),
                        cls         : 'desc-under'
                    }]
                }, {
                    columnWidth : .5,
                    items       : [{
                        xtype       : 'toolbar',
                        items       : [{
                            cls         : 'x-btn-no-text',
                            text        : '<i class="icon icon-desktop"></i>',
                            preview     : 'desktop',
                            handler     : this.onChangePreview,
                            scope       : this,
                            listeners    : {
                                afterrender : {
                                    fn          : this.onChangePreview,
                                    scope       : this
                                }
                            }
                        }, {
                            cls         : 'x-btn-no-text',
                            text        : '<i class="icon icon-mobile"></i>',
                            preview     : 'mobile',
                            handler     : this.onChangePreview,
                            scope       : this
                        }]
                    }, {
                        xtype       : 'panel',
                        baseCls     : 'seosuite-preview',
                        id          : 'seosuite-seo-preview',
                        cls         : 'seosuite-seo-preview-' + SeoSuite.config.meta.preview.search_engine,
                        items       : [{
                            id          : 'seosuite-seo-preview-favicon',
                            html        : '<img src="' + SeoSuite.record.favicon + '" class="favicon" />'
                        }, {
                            id          : 'seosuite-seo-preview-title'
                        }, {
                            id          : 'seosuite-seo-preview-url',
                            html        : this.getUrlHTML()
                        }, {
                            id          : 'seosuite-seo-preview-description'
                        }]
                    }, {
                        xtype       : 'xcheckbox',
                        hideLabel   : true,
                        boxLabel    : _('seosuite.tab_meta.use_default'),
                        name        : 'seosuite_use_default_meta',
                        id          : 'seosuite_use_default_meta',
                        inputValue  : 1,
                        checked     : SeoSuite.record.use_default_meta,
                        listeners   : {
                            check       : {
                                fn          : this.onChangeMetaDefault,
                                scope       : this
                            },
                            afterrender : {
                                fn          : this.onChangeMetaDefault,
                                scope       : this
                            }
                        }
                    }, {
                        xtype       : 'seosuite-field-metatag',
                        fieldLabel  : _('seosuite.tab_meta.meta_title'),
                        description : _('seosuite.tab_meta.meta_title_desc'),
                        anchor      : '100%',
                        name        : 'seosuite_meta_title',
                        id          : 'title',
                        value       : Ext.encode(SeoSuite.record.meta_title),
                        listeners   : {
                            change    : {
                                fn          : this.onRenderPreview,
                                scope       : this
                            }
                        }
                    }, {
                        xtype       : 'seosuite-field-metatag',
                        fieldLabel  : _('seosuite.tab_meta.meta_description'),
                        description : _('seosuite.tab_meta.meta_description_desc'),
                        anchor      : '100%',
                        name        : 'seosuite_meta_description',
                        id          : 'description',
                        value       : Ext.encode(SeoSuite.record.meta_description),
                        listeners   : {
                            change    : {
                                fn          : this.onRenderPreview,
                                scope       : this
                            }
                        }
                    }]
                }]
            }]
        });

        fp.doLayout();
    },
    addCounter: function(fieldKey) {
        var fieldId = this.getFieldId(fieldKey);
        var field = Ext.getCmp(fieldId);

        if (field) {
            SeoSuite.record.values[fieldKey] = field.getValue();

            field.on('keyup', function() {
                SeoSuite.record.values[fieldKey] = field.getValue();
                SeoSuite.count(fieldKey);
            });

            field.on('blur', function() {
                SeoSuite.record.values[fieldKey] = field.getValue();
            });

            var counterHtml = '<span class="seosuite-counter-wrap seosuite-counter-keywords" id="seosuite-counter-keywords-' + fieldKey + '" title="' + _('seosuite.tab_meta.keywords') + '"><strong>' + _('seosuite.tab_meta.keywords') + ':&nbsp;&nbsp;</strong><span id="seosuite-counter-keywords-' + fieldKey + '-current">0</span></span>';
            if (fieldKey !== 'content') {
                var chartHtml = '<svg viewBox="0 0 36 36" class="circular-chart">\n' +
                                '  <path class="circle" id="seosuite-counter-circle-' + fieldKey + '"\n' +
                                '    stroke-dasharray="0px, 100px"\n' +
                                '    d="M18 2.0845\n' +
                                '      a 15.9155 15.9155 0 0 1 0 31.831\n' +
                                '      a 15.9155 15.9155 0 0 1 0 -31.831"\n' +
                                '  />\n' +
                                '</svg>';

                counterHtml += '<span class="seosuite-counter-wrap seosuite-counter-chars green" id="seosuite-counter-chars-' + fieldKey + '" title="' + _('seosuite.tab_meta.characters.allowed') + '">' + chartHtml + '<span class="current" id="seosuite-counter-chars-' + fieldKey + '-current">1</span>';
                counterHtml += '<span class="allowed" id="seosuite-counter-chars-' + fieldKey + '-allowed">' + SeoSuite.record.chars[fieldKey]['max'] + '</span></span>';
            }

            Ext.get('x-form-el-' + fieldId).createChild({
                tag   : 'div',
                id    : 'seosuite-resource-' + fieldKey,
                class : 'seosuite-counter',
                html  : counterHtml
            });

            SeoSuite.count(fieldKey);
        }
    },
    countCharacters: function (fieldKey, overrideCount) {
        var field    = this.getFieldId(fieldKey);
        var value    = Ext.getCmp(field).getValue();
        var maxChars = Ext.get('seosuite-counter-chars-' + fieldKey + '-allowed').dom.innerHTML;
        var tooLong  = false;
        var tooShort = false;
        var charCount;

        /* Title and description counts are updated via Ajax request and will contain the overrideCount parameter. */
        if ((fieldKey === 'longtitle' || fieldKey === 'description') && typeof overrideCount === 'undefined') {
            return '';
        }

        charCount = overrideCount ? overrideCount : value.length;
        if (charCount > maxChars) {
            tooLong = true;
        } else if (charCount < SeoSuite.record.chars[fieldKey]['min']) {
            tooShort = true;
        }

        if (tooLong || tooShort) {
            if (tooShort && charCount > (SeoSuite.record.chars[fieldKey]['min'] - 10)) {
                Ext.get('seosuite-counter-chars-' + fieldKey).addClass('orange').removeClass('green').removeClass('red');
            } else {
                Ext.get('seosuite-counter-chars-' + fieldKey).addClass('red').removeClass('green').removeClass('orange');
            }
        } else {
            Ext.get('seosuite-counter-chars-' + fieldKey).removeClass('red').removeClass('orange').addClass('green');
        }

        Ext.get('seosuite-counter-chars-' + fieldKey + '-current').dom.innerHTML = charCount;

        /* Update character count circle. */
        var percentage = Math.round((charCount / maxChars) * 100);
        var circle = document.querySelector('#seosuite-counter-circle-' + fieldKey);

        /**
         * Using animate to support stroke-dasharray in IE.
         * @see(https://github.com/web-animations/web-animations-js)
         */
        circle.animate([{
            'strokeDasharray': percentage + 'px, 100px',
            easing           : 'cubic-bezier(0.4, 0, 0.2, 1)',
            offset           : 0
        }, {
            'strokeDasharray': percentage + 'px, 100px',
            offset           : 1
        }], {
            duration: 600,
            fill    : 'forwards'
        });
    },
    countKeywords: function (fieldKey) {
        var field        = this.getFieldId(fieldKey);
        var value        = Ext.get(field).getValue();
        var keywordCount = 0;

        Ext.each(Ext.get('seosuite-keywords').getValue().split(','), function(keyword) {
            keyword = keyword.replace(/^\s+/, '').toLowerCase();

            /* Longtitle has fallback on pagetitle, the keyword count is updated here. */
            if (fieldKey === 'longtitle' && value.length === 0) {
                value = Ext.getCmp('modx-resource-pagetitle').getValue();
            }

            if (keyword) {
                var counter = value.toLowerCase().match(new RegExp("(^|[ \s\n\r\t\.,'\(\"\+;!?:\-])" + keyword + "($|[ \s\n\r\t.,'\)\"\+!?:;\-])", 'gim'));
                if (counter) {
                    keywordCount = keywordCount + counter.length;
                }
            }
        });

        Ext.get('seosuite-counter-keywords-' + fieldKey + '-current').dom.innerHTML = keywordCount;

        var maxKeywords = MODx.isEmpty(this.config.meta.max_keywords_title) ? '4' : this.config.meta.max_keywords_title;
        if (fieldKey === 'description') {
            /* Use different limit for the description. */
            maxKeywords = MODx.isEmpty(this.config.meta.max_keywords_description) ? '8' : this.config.meta.max_keywords_description;
        }

        maxKeywords = parseInt(maxKeywords);
        if (keywordCount > 0 && keywordCount <= maxKeywords) {
            Ext.get('seosuite-counter-keywords-' + fieldKey).removeClass('red');
        } else {
            Ext.get('seosuite-counter-keywords-' + fieldKey).addClass('red');
        }
    },
    count: function(field, overrideCount) {
        if (field !== 'content') {
            this.countCharacters(field, overrideCount);
        }

        this.countKeywords(field);
    },
    getFieldId: function (fieldKey) {
        var fieldId = fieldKey;
        if (fieldKey === 'longtitle' || fieldKey === 'description') {
            fieldId = 'seosuite-' + fieldId;
        } else if (fieldKey === 'content') {
            fieldId = 'ta';
        } else {
            fieldId = 'modx-resource-' + fieldId;
        }

        return fieldId;
    },
    onRenderPreview: function () {
        console.log('onRenderPreview');

        MODx.Ajax.request({
            url     : SeoSuite.config.connector_url,
            params  : {
                action      : 'mgr/resource/preview',
                title       : Ext.getCmp('seosuite-preview-editor-title').getValue(),
                description : Ext.getCmp('seosuite-preview-editor-description').getValue(),
                fields      : Ext.encode({
                    pagetitle    : Ext.getCmp('modx-resource-pagetitle').getValue(),
                    longtitle    : Ext.getCmp('modx-resource-longtitle').getValue(),
                    description  : Ext.getCmp('modx-resource-description').getValue(),
                    introtext    : Ext.getCmp('modx-resource-introtext').getValue()
                }),
                content_type        : Ext.getCmp('modx-resource-content-type').getValue(),
                alias               : Ext.getCmp('modx-resource-alias').getValue(),
                uri                 : Ext.getCmp('modx-resource-uri').getValue(),
                uri_override        : Ext.getCmp('modx-resource-uri-override').getValue(),
                use_default_meta    : Ext.getCmp('seosuite_use_default_meta').getValue(),
                context             : MODx.ctx,
                resource            : MODx.activePage.resource,
                preview_type        : this.previewMode
            },
            listeners: {
                'success': {
                    fn: function(response) {
                        Ext.get('seosuite-seo-preview-title').dom.innerHTML       = response.results.output.title;
                        Ext.get('seosuite-seo-preview-description').dom.innerHTML = response.results.output.description;
                        Ext.get('seosuite-replace-alias').dom.innerHTML       = response.results.output.alias;

                        /* Update counters. */
                        this.countCharacters('longtitle', response.results.counts.title);
                        this.countCharacters('description', response.results.counts.description);
                    },
                    scope: this
                }
            }
        });
    },
    onChangeMetaDefault: function (tf) {
        console.log('onChangeMetaDefault', tf);

        var metaTitle       = Ext.getCmp('title');
        var metaDescription = Ext.getCmp('description');

        if (metaTitle && metaDescription) {
            if (tf.getValue()) {
                metaTitle.hide();
                metaDescription.hide();

                //metaTitle.setValue(SeoSuite.config.meta.default_meta_title);
                //metaDescription.setValue(SeoSuite.config.meta.default_meta_description);
            } else {
                metaTitle.show();
                metaDescription.show();
            }
        }

        this.onRenderPreview();
    },
    onChangePreview: function(btn) {
        console.log('onChangePreview', btn);

        this.previewMode = btn.preview;

        var preview = Ext.getCmp('seosuite-seo-preview');

        if (preview && btn.ownerCt.items) {
            btn.ownerCt.items.items.forEach(function (item) {
                if (item.preview) {
                    if (item.preview === btn.preview) {
                        item.addClass('x-btn-active');

                        preview.addClass('seosuite-seo-preview-' + item.preview);
                    } else {
                        item.removeClass('x-btn-active');

                        preview.removeClass('seosuite-seo-preview-' + item.preview);
                    }
                }
            });
        }

        this.onRenderPreview();
    },
    getUrlHTML: function () {
        var html = '<img src="' + SeoSuite.record.favicon + '" class="favicon" />';

        if (MODx.config.server_protocol === 'https') {
            html += '<i class="icon icon-lock"></i> ';
        }

        html += SeoSuite.record.url;

        return html;
    }
});

Ext.onReady(function() {
    if (!SeoSuite.config.loaded) {
        SeoSuite.initialize();
    }
});
