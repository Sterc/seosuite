Ext.extend(SeoSuite, Ext.Component, {
    initialize: function() {
        SeoSuite.config.loaded = true;
        SeoSuite.addPanel();

        var self = this;

        /**
         * Hide default longtitle and description fields, because SeoSuite adds their own and mirrors the content to the default fields.
         */
        Ext.getCmp('modx-resource-longtitle').hide();
        Ext.getCmp('modx-resource-description').hide();

        /* Live update preview when these fields change. */
        ['modx-resource-pagetitle', 'modx-resource-introtext', 'modx-resource-alias', 'modx-resource-uri', 'seosuite-longtitle', 'seosuite-description']
            .map(document.getElementById, document)
            .forEach(function (elem) {
                elem.addEventListener('keyup', function() {
                    self.renderPreview();

                    if (elem.id === 'modx-resource-pagetitle') {
                        if (Ext.getCmp('seosuite-longtitle')) {
                            var isEmpty = Ext.getCmp('seosuite-longtitle').getValue().length == 0;
                            Ext.getCmp('seosuite-longtitle').emptyText = Ext.getCmp(elem.id).getValue();
                            Ext.getCmp('seosuite-longtitle').applyEmptyText();

                            if (isEmpty) {
                                Ext.getCmp('seosuite-longtitle').reset();
                            }
                        }

                        /* Live update keyword count for longtitle field, because of the fallback. */
                        self.count('longtitle');
                    }
                });
            });

        /* Live update preview when these fields change. */
        ['modx-resource-uri-override', 'seosuite_use_default_meta']
            .map(document.getElementById, document)
            .forEach(function (elem) {
                elem.addEventListener('change', function() {
                    self.renderPreview();
                });
            });

        Ext.each(SeoSuite.record.fields.split(','), function(field) {
            SeoSuite.addCounter(field);
        });
    },
    addCounter: function(fieldKey) {
        var fieldId = this.getFieldId(fieldKey);
        var field = Ext.getCmp(fieldId);

        if (field) {
            SeoSuite.record.values[fieldKey] = field.getValue();

            if (fieldKey !== 'content') {
                field.maxLength = Number(SeoSuite.record.chars[fieldKey]['max']);
                field.reset();
            }

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
    addPanel: function() {
        var fp = Ext.getCmp('modx-panel-resource');

        fp.insert(2, {
                xtype        : 'panel',
                anchor       : '100%',
                border       : false,
                fieldLabel   : (this.config.meta.search_engine === 'yandex' ? _('seosuite.tab_meta.prevbox_yandex') : _('seosuite.tab_meta.prevbox')),
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
                    anchor      : '100%',
                    defaults    : {
                        layout          : 'form',
                        labelSeparator  : ''
                    },
                    items: [{
                        columnWidth : .5,
                        items       : [{
                            anchor          : '100%',
                            xtype           : 'panel',
                            id              : 'seosuite-preview-editor',
                            baseCls         : 'seosuite-preview-editor',
                            border          : false,
                            autoHeight      : true,
                            layout          : 'form',
                            labelSeparator  : '',
                            labelAlign      : 'top',
                            items           : [{
                                xtype           : 'textfield',
                                name            : 'seosuite_keywords',
                                id              : 'seosuite-keywords',
                                fieldLabel      : _('seosuite.tab_meta.focuskeywords'),
                                description     : MODx.expandHelp ? '' : _('seosuite.tab_meta.focuskeywords_desc'),
                                value           : SeoSuite.record.keywords,
                                enableKeyEvents : true,
                                anchor          : '100%',
                                listeners       : {
                                    'keyup': function () {
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
                                xtype           : 'textfield',
                                name            : 'seosuite_longtitle',
                                id              : 'seosuite-longtitle',
                                fieldLabel      : _('seosuite.tab_meta.longtitle'),
                                emptyText       : MODx.activePage.record.pagetitle,
                                value           : MODx.activePage.record.longtitle,
                                enableKeyEvents : true,
                                anchor          : '100%',
                                listeners       : {
                                    'keyup' : function (field) {
                                        Ext.getCmp('modx-resource-longtitle').setValue(field.getValue());
                                    },
                                    scope    : this
                                }
                            }, {
                                xtype           : 'textarea',
                                name            : 'seosuite_description',
                                id              : 'seosuite-description',
                                fieldLabel      : _('seosuite.tab_meta.description'),
                                value           : MODx.activePage.record.description,
                                enableKeyEvents : true,
                                anchor          : '100%',
                                listeners       : {
                                    'keyup' : function (field) {
                                        Ext.getCmp('modx-resource-description').setValue(field.getValue());
                                    },
                                    scope    : this
                                }
                            }]
                        }]
                    }, {
                        columnWidth : .5,
                        items       : [{
                            xtype       : 'button',
                            cls         : 'active',
                            id          : 'seosuite-preview-mobile',
                            text        : '<i class="icon icon-mobile"></i>',
                            handler     : function () {
                                this.addClass('active');

                                Ext.select('#seosuite-preview-desktop').removeClass('active');
                                Ext.select('.seosuite-preview').addClass('mobile').removeClass('desktop');
                            }
                        }, {
                            xtype   : 'button',
                            id      : 'seosuite-preview-desktop',
                            text    : '<i class="icon icon-desktop"></i>',
                            handler : function () {
                                this.addClass('active');

                                Ext.select('#seosuite-preview-mobile').removeClass('active');
                                Ext.select('.seosuite-preview').removeClass('mobile').addClass('desktop');
                            }
                        }, {
                            xtype       : 'button',
                            id          : 'seosuite-snippet-editor',
                            text        : '<i class="icon icon-cog"></i>',
                            handler     : function () {
                                if (Ext.getCmp('seosuite-preview-editor').hidden) {
                                    Ext.getCmp('seosuite-preview-editor').show();
                                } else {
                                    Ext.getCmp('seosuite-preview-editor').hide();
                                }
                            }
                        }, {
                            xtype       : 'panel',
                            baseCls     : 'seosuite-preview',
                            cls         : this.config.meta.search_engine + ' mobile',
                            bodyStyle   : 'padding: 10px;',
                            border      : false,
                            autoHeight  : true,
                            items       : [{
                                xtype       : 'box',
                                id          : 'seosuite-preview-url',
                                bodyStyle   : 'background-color: #fbfbfb;',
                                cls         : this.config.meta.search_engine,
                                html        : this.generateUrlHtml(),
                                border      : false
                            }, {
                                xtype       : 'box',
                                id          : 'seosuite-preview-title',
                                cls         : this.config.meta.search_engine,
                                html        : '',
                                border      : false
                            }, {
                                xtype       : 'box',
                                id          : 'seosuite-preview-description',
                                bodyStyle   : 'background-color: #fbfbfb;',
                                cls         : this.config.meta.search_engine,
                                html        : '',
                                border      : false
                            }]
                        }, {
                            anchor          : '100%',
                            xtype           : 'panel',
                            id              : 'seosuite-preview-editor',
                            baseCls         : 'seosuite-preview-editor',
                            bodyStyle       : 'padding: 10px;',
                            border          : false,
                            autoHeight      : true,
                            layout          : 'form',
                            labelSeparator  : '',
                            labelAlign      : 'top',
                            hidden          : true,
                            items: [
                                {
                                    xtype       : 'checkbox',
                                    name        : 'seosuite_use_default_meta',
                                    id          : 'seosuite_use_default_meta',
                                    boxLabel    : _('seosuite.tab_meta.use_default'),
                                    inputValue  : 1,
                                    checked     : SeoSuite.record.use_default_meta,
                                    listeners    : {
                                        'render'   : this.onChangeUseDefault,
                                        'check'    : this.onChangeUseDefault
                                    }
                                }, {
                                    xtype      : 'seosuite-field-metatag',
                                    label      : _('seosuite.tab_meta.meta_title'),
                                    name       : 'seosuite_meta_title',
                                    description: _('seosuite.tab_meta.meta_title_desc'),
                                    id         : 'title',
                                    value      : JSON.stringify(SeoSuite.record.meta_title),
                                    listeners  : {
                                        'change'    : {
                                            fn          : function() {
                                                this.renderPreview();
                                            },
                                            scope       : this
                                        }
                                    }
                                }, {
                                    xtype      : 'seosuite-field-metatag',
                                    label      : _('seosuite.tab_meta.meta_description'),
                                    name       : 'seosuite_meta_description',
                                    description: _('seosuite.tab_meta.meta_description_desc'),
                                    id         : 'description',
                                    value      : JSON.stringify(SeoSuite.record.meta_description),
                                    listeners  : {
                                        'change'    : {
                                            fn          : function() {
                                                this.renderPreview();
                                            },
                                            scope       : this
                                        }
                                    }
                                }]
                        }]
                    }]
                }]
            }
        );

        fp.doLayout();

        this.renderPreview();
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
    generateUrlHtml: function () {
        var  html = '<div class="seosuite-preview-url--favicon" style="background-image: url(' + SeoSuite.record.favicon + ')"></div>';

        if (MODx.config['seosuite.preview.searchengine'] === 'yandex' && MODx.config['server_protocol'] === 'https') {
            html += '<i class="icon icon-lock"></i> ';
        }

        html += SeoSuite.record.url;

        return html;
    },
    renderPreview: function () {
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
                resource            : MODx.activePage.resource
            },
            listeners: {
                'success': {
                    fn: function(response) {
                        Ext.get('seosuite-preview-title').dom.innerHTML       = response.results.output.title;
                        Ext.get('seosuite-preview-description').dom.innerHTML = response.results.output.description;
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
    onChangeUseDefault: function (checkbox) {
        if (checkbox.getValue()) {
            Ext.getCmp('title').hide();
            Ext.getCmp('description').hide();
        } else {
            Ext.getCmp('title').show();
            Ext.getCmp('description').show();
        }
    }
});

Ext.onReady(function() {
    if (!SeoSuite.config.loaded) {
        SeoSuite.initialize();
    }
});
