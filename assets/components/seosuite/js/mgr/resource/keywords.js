Ext.extend(SeoSuite, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {},
    initialize: function() {
        SeoSuite.config.loaded       = true;
        SeoSuite.config.delimiter    = MODx.isEmpty(MODx.config['seosuite.preview.delimiter']) ? '|' : MODx.config['seosuite.preview.delimiter'];
        SeoSuite.config.siteNameShow = !MODx.isEmpty(MODx.config['seosuite.preview.usesitename']);
        SeoSuite.config.searchEngine = MODx.isEmpty(MODx.config['seosuite.preview.searchengine']) ? 'google' : MODx.config['seosuite.preview.searchengine'];
        SeoSuite.config.titleFormat  = MODx.isEmpty(MODx.config['seosuite.preview.title_format']) ? '' : MODx.config['seosuite.preview.title_format'];
        SeoSuite.addKeywords();
        SeoSuite.addPanel();

        Ext.each(SeoSuite.config.fields.split(','), function(field) {
            SeoSuite.addCounter(field);
            if (field !== 'alias' && field !== 'menutitle') {
                SeoSuite.changePrevBox(field);
            }
        });
        Ext.getCmp('modx-panel-resource').on('success', function() {
            if(Ext.get('seosuite-replace-alias')) {
                Ext.get('seosuite-replace-alias').dom.innerHTML = this.record.alias;
            }
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
                SeoSuite.changePrevBox(field);
            });
            Field.on('blur', function() {
                SeoSuite.config.values[field] = Field.getValue();
                SeoSuite.changePrevBox(field);
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
            value           : SeoSuite.config.record,
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

        fp.insert(3, field);
        fp.doLayout();
    },
    addPanel: function() {
        var fp = Ext.getCmp('modx-resource-main-left');

        fp.insert(5, {
            xtype       : 'panel',
            anchor      : '100%',
            border      : false,
            fieldLabel  : (SeoSuite.config.searchEngine == 'yandex' ? _('seosuite.prevbox_yandex') : _('seosuite.prevbox')),
            layout      : 'form',
            items       : [{
                columnWidth : .67,
                xtype       : 'panel',
                baseCls     : 'seosuite-panel',
                cls         : SeoSuite.config.searchEngine,
                bodyStyle   : 'padding: 10px;',
                border      : false,
                autoHeight  : true,
                items       : [{
                    xtype       : 'box',
                    id          : 'seosuite-google-title',
                    cls         : SeoSuite.config.searchEngine,
                    html        : '',
                    border      : false
                }, {
                    xtype       : 'box',
                    id          : 'seosuite-google-url',
                    bodyStyle   : 'background-color: #fbfbfb;',
                    cls         : SeoSuite.config.searchEngine,
                    html        : SeoSuite.config.url,
                    border      : false
                }, {
                    xtype       : 'box',
                    id          : 'seosuite-google-description',
                    bodyStyle   : 'background-color: #fbfbfb;',
                    cls         : SeoSuite.config.searchEngine,
                    html        : '',
                    border      : false
                }]
            }]
        });
        fp.doLayout();

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
    changePrevBox: function(field) {
        switch (field) {
            case 'pagetitle':
            case 'longtitle':
                var title;
                var resourceId = MODx.request.id;
                var pagetitle  = Ext.get('modx-resource-pagetitle').getValue();
                var longtitle  = Ext.get('modx-resource-longtitle').getValue();

                if (SeoSuite.config.titleFormat && resourceId) {
                    MODx.Ajax.request({
                        url     : SeoSuite.config.connectorUrl,
                        params  : {
                            action      : 'mgr/resource/searchpreview',
                            id          : resourceId,
                            pagetitle   : pagetitle,
                            longtitle   : longtitle,
                            html        : SeoSuite.config.titleFormat,
                        },
                        listeners: {
                            'success':{
                                fn:function(r) {
                                    Ext.get('seosuite-google-title').dom.innerHTML = r.results.output;

                                    SeoSuite.count(field, title.length);
                                },
                                scope:this
                            }
                        }
                    });
                } else {
                    title = SeoSuite.config.values['pagetitle'];
                    if (!MODx.isEmpty(SeoSuite.config.values['longtitle'])) {
                        title = SeoSuite.config.values['longtitle'];
                    }

                    if (SeoSuite.config.siteNameShow) {
                        title += ' ' + SeoSuite.config.delimiter + ' ' + MODx.config.site_name;
                    }

                    Ext.get('seosuite-google-title').dom.innerHTML = title;
                }
                break;
            case 'description' :
            case 'introtext'   :
                var description;

                if (MODx.isEmpty(SeoSuite.config.values['description'])) {
                    var introCheck = Ext.getCmp('modx-resource-description');
                    if (!MODx.isEmpty(SeoSuite.config.values['introtext']) && !introCheck) {
                        description = SeoSuite.config.values['introtext'];
                    } else {
                        var label = Ext.get('modx-resource-description').dom.labels[0].innerText;

                        label       = label.replace(/:$/, '').toLowerCase();
                        description = _('seosuite.emptymetadescription');
                        description = description.replace(/\<span class="seosuite-google-description--field"\>(.*)\<\/span\>/, label);
                    }
                } else {
                    description = SeoSuite.config.values['description'];
                }

                Ext.get('seosuite-google-description').dom.innerHTML = description;
                break;
            case 'alias':
                Ext.get('seosuite-replace-alias').dom.innerHTML = SeoSuite.config.values['alias'];
                break;
        }
    }
});

Ext.onReady(function() {
    if (!SeoSuite.config.loaded) {
        SeoSuite.initialize();
    }
});