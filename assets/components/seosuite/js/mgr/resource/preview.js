Ext.extend(SeoSuite, Ext.Component, {
    initialize: function() {
        SeoSuite.config.loaded       = true;
        SeoSuite.config.delimiter    = MODx.isEmpty(MODx.config['seosuite.preview.delimiter']) ? '|' : MODx.config['seosuite.preview.delimiter'];
        SeoSuite.config.siteNameShow = !MODx.isEmpty(MODx.config['seosuite.preview.usesitename']);
        SeoSuite.config.searchEngine = MODx.isEmpty(MODx.config['seosuite.preview.searchengine']) ? 'google' : MODx.config['seosuite.preview.searchengine'];
        SeoSuite.config.titleFormat  = MODx.isEmpty(MODx.config['seosuite.preview.title_format']) ? '' : MODx.config['seosuite.preview.title_format'];
        SeoSuite.addKeywords();
        SeoSuite.addPanel();

        var self = this;

        /* Live update preview when these fields change. */
        ['modx-resource-pagetitle', 'modx-resource-longtitle', 'modx-resource-description', 'modx-resource-introtext', 'modx-resource-alias', 'modx-resource-uri']
            .map(document.getElementById, document)
            .forEach(function (elem) {
                elem.addEventListener('keyup', function() {
                    self.renderPreview();
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

        Ext.each(SeoSuite.config.fields.split(','), function(field) {
            SeoSuite.addCounter(field);
        });
    },
    addCounter: function(field) {
        var Field = Ext.getCmp('modx-resource-' + field);
        if (Field) {
            SeoSuite.config.values[field] = Field.getValue();
            Field.maxLength = Number(SeoSuite.config.chars[field]);
            Field.reset();

            Field.on('keyup', function() {
                SeoSuite.config.values[field] = Field.getValue();
                SeoSuite.count(field);
            });

            Field.on('blur', function() {
                SeoSuite.config.values[field] = Field.getValue();
            });

            Ext.get('x-form-el-modx-resource-' + field).createChild({
                tag: 'div',
                id: 'seosuite-resource-' + field,
                class: 'seosuite-counter',
                html: '<span class="seosuite-counter-wrap seosuite-counter-keywords" id="seosuite-counter-keywords-' + field + '" title="' + _('seosuite.keywords') + '"><strong>' + _('seosuite.keywords') + ':&nbsp;&nbsp;</strong><span id="seosuite-counter-keywords-' + field + '-current">0</span></span>\
                        <span class="seosuite-counter-wrap seosuite-counter-chars green" id="seosuite-counter-chars-' + field + '" title="' + _('seosuite.characters.allowed') + '"><span class="current" id="seosuite-counter-chars-' + field + '-current">1</span>/<span class="allowed" id="seosuite-counter-chars-' + field + '-allowed">' + SeoSuite.config.chars[field] + '</span></span>'
            });
            SeoSuite.count(field);
        }
    },
    addKeywords: function() {
        var fp = Ext.getCmp('modx-resource-main-left');
        var field = new Ext.form.TextField({
            xtype           : 'textfield',
            name            : 'keywords',
            id              : 'seosuite-keywords',
            fieldLabel      : _('seosuite.focuskeywords'),
            description     : _('seosuite.focuskeywords_desc'),
            value           : SeoSuite.config.record.keywords,
            enableKeyEvents : true,
            anchor          : '100%',
            listeners       : {
                'keyup': function() {
                    MODx.fireResourceFormChange();

                    Ext.each(SeoSuite.config.fields.split(','), function(field) {
                        var Field = Ext.getCmp('modx-resource-' + field);
                        if (Field) {
                            SeoSuite.count(field);
                        }
                    });
                }
            }
        });

        fp.insert(4, field);
        fp.doLayout();
    },
    addPanel: function() {
        var fp = Ext.getCmp('modx-resource-settings');

        fp.insert(2, {
                xtype       : 'panel',
                anchor      : '100%',
                border      : false,
                fieldLabel  : (SeoSuite.config.searchEngine == 'yandex' ? _('seosuite.prevbox_yandex') : _('seosuite.prevbox')),
                layout      : 'form',
                items       : [{
                    layout      : 'column',
                    anchor      : '100%',
                    defaults    : {
                        layout          : 'form',
                        labelSeparator  : ''
                    },
                    items: [{
                        columnWidth : .5,
                        items       : [{
                            xtype       : 'button',
                            id          : 'seosuite-snippet-editor',
                            text        : '<i class="icon icon-pencil"></i> ' + 'Edit snippet',
                            handler     : function () {
                                if (Ext.getCmp('seosuite-preview-editor').hidden) {
                                    Ext.getCmp('seosuite-preview-editor').show();
                                } else {
                                    Ext.getCmp('seosuite-preview-editor').hide();
                                }
                            }
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
                            hidden          : false,
                            items: [
                                {
                                    xtype       : 'checkbox',
                                    name        : 'seosuite_use_default_meta',
                                    id          : 'seosuite_use_default_meta',
                                    boxLabel    : _('seosuite.meta.use_default'),
                                    inputValue  : 1,
                                    checked     : SeoSuite.config.record.use_default_meta,
                                    listeners    : {
                                        'render'   : this.onChangeUseDefault,
                                        'check'    : this.onChangeUseDefault
                                    }
                                }, {
                                xtype   : 'seosuite-field-metatag',
                                label   : _('seosuite.meta_title'),
                                name    : 'seosuite_meta_title',
                                id      : 'title',
                                value   : JSON.stringify(SeoSuite.config.record.meta_title),
                                listeners   : {
                                    'change'    : {
                                        fn          : function() {
                                            this.renderPreview();
                                        },
                                        scope       : this
                                    }
                                }
                            }, {
                                xtype   : 'seosuite-field-metatag',
                                label   : _('seosuite.meta_description'),
                                name    : 'seosuite_meta_description',
                                id      : 'description',
                                value   : JSON.stringify(SeoSuite.config.record.meta_description),
                                listeners   : {
                                    'change'    : {
                                        fn          : function() {
                                            this.renderPreview();
                                        },
                                        scope       : this
                                    }
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
                                Ext.select('.seosuite-preview').addClass('mobile');
                            }
                        }, {
                            xtype   : 'button',
                            id      : 'seosuite-preview-desktop',
                            text    : '<i class="icon icon-desktop"></i>',
                            handler : function () {
                                this.addClass('active');

                                Ext.select('#seosuite-preview-mobile').removeClass('active');
                                Ext.select('.seosuite-preview').removeClass('mobile');
                            }
                        }, {
                            xtype       : 'panel',
                            baseCls     : 'seosuite-preview',
                            cls         : SeoSuite.config.searchEngine + ' mobile',
                            bodyStyle   : 'padding: 10px;',
                            border      : false,
                            autoHeight  : true,
                            items       : [{
                                xtype       : 'box',
                                id          : 'seosuite-preview-title',
                                cls         : SeoSuite.config.searchEngine,
                                html        : '',
                                border      : false
                            }, {
                                xtype       : 'box',
                                id          : 'seosuite-preview-url',
                                bodyStyle   : 'background-color: #fbfbfb;',
                                cls         : SeoSuite.config.searchEngine,
                                html        : SeoSuite.config.url,
                                border      : false
                            }, {
                                xtype       : 'box',
                                id          : 'seosuite-preview-description',
                                bodyStyle   : 'background-color: #fbfbfb;',
                                cls         : SeoSuite.config.searchEngine,
                                html        : '',
                                border      : false
                            }]
                        }]
                    }]
                }]
            }
        );

        fp.doLayout();
        
        this.renderPreview();
    },
    count: function(field, overrideCount) {
        var Value    = Ext.get('modx-resource-' + field).getValue();
        var maxchars = Ext.get('seosuite-counter-chars-' + field + '-allowed').dom.innerHTML;
        var charCount;

        if (overrideCount) {
            charCount = overrideCount;
        } else {
            charCount = Value.length;
            if (SeoSuite.config.siteNameShow && (field === 'pagetitle' || field === 'longtitle')) {
                var extra = ' ' + SeoSuite.config.delimiter + ' ' + MODx.config.site_name;
                charCount = charCount + extra.length;
            }
        }

        var keywordCount = 0;
        Ext.each(Ext.get('seosuite-keywords').getValue().split(','), function(keyword) {
            keyword = keyword.replace(/^\s+/, '').toLowerCase();

            if (keyword) {
                var counter = Value.toLowerCase().match(new RegExp("(^|[ \s\n\r\t\.,'\(\"\+;!?:\-])" + keyword + "($|[ \s\n\r\t.,'\)\"\+!?:;\-])", 'gim'));
                if (counter) {
                    keywordCount = keywordCount + counter.length;
                }
            }
        });

        Ext.get('seosuite-counter-chars-' + field + '-current').dom.innerHTML = charCount;
        Ext.get('seosuite-counter-keywords-' + field + '-current').dom.innerHTML = keywordCount;

        var maxKeywords = MODx.isEmpty(MODx.config['seosuite.keywords.max_keywords_title']) ? '4' : MODx.config['seosuite.keywords.max_keywords_title'];
        if (field === 'description') {
            /* Use different limit for the description. */
            maxKeywords = MODx.isEmpty(MODx.config['seosuite.keywords.max_keywords_description']) ? '8' : MODx.config['seosuite.keywords.max_keywords_description'];
        }
        maxKeywords = parseInt(maxKeywords);

        if (keywordCount > 0 && keywordCount <= maxKeywords) {
            Ext.get('seosuite-counter-keywords-' + field).removeClass('red');
        } else {
            Ext.get('seosuite-counter-keywords-' + field).addClass('red');
        }

        if (charCount > maxchars || charCount === 0) {
            Ext.get('seosuite-counter-chars-' + field).addClass('red').removeClass('green');
        } else {
            Ext.get('seosuite-counter-chars-' + field).addClass('green').removeClass('red');
        }
    },
    renderPreview: function () {
        MODx.Ajax.request({
            url     : SeoSuite.config.connectorUrl,
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
