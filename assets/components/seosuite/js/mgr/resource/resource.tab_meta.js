Ext.extend(SeoSuite, Ext.Component, {
    initialize: function() {
        SeoSuite.config.loaded = true;
        SeoSuite.addPanel();

        /* MODX 3 specific fix. */
        if (parseInt(MODx.config.version.split('.')[0]) === 3) {
            /* Fix introtext width when it's supposed to show next to description field. */
            Ext.getCmp('modx-resource-introtext').findParentByType('panel').columnWidth = 1;
            Ext.getCmp('modx-resource-introtext').findParentByType('panel').removeClass('x-column');
            Ext.getCmp('modx-resource-introtext').findParentByType('panel').doLayout();
        }

        ['modx-resource-pagetitle', 'modx-resource-introtext', 'modx-resource-alias', 'modx-resource-uri', 'modx-resource-uri-override', 'modx-resource-parent', 'seosuite-longtitle', 'seosuite-description'].forEach((function(key) {
            var field = Ext.getCmp(key);

            if (field) {
                if (field.xtype === 'xcheckbox') {
                    field.on('check', this.onRenderPreview, this);
                } else {
                    field.on('keyup', this.onRenderPreview, this);
                    field.on('change', this.onRenderPreview, this);
                }
            }
        }).bind(this));

        Ext.iterate(this.getFieldCounters(), (function(key, length) {
            this.onAddCounter(key, length.min || 0, length.max || 0);
        }).bind(this));

        Ext.iterate(this.getFieldKeywordCounters(), (function(key, maxKeywords) {
            this.onAddKeywordCounter(key, maxKeywords);
        }).bind(this));

        ['modx-resource-longtitle', 'modx-resource-description'].forEach(function(key) {
            var field = Ext.getCmp(key);

            if (field) {
                field.hide();
            }
        });
    },
    addPanel: function() {
        var panel = Ext.getCmp('modx-panel-resource');

        if (panel) {
            panel.insert(2, {
                xtype        : 'panel',
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
                    html            : parseInt(MODx.config.friendly_urls) === 0 ? '<p>' + _('seosuite.friendly_urls_disabled') + '</p>' : '',
                    cls             : parseInt(MODx.config.friendly_urls) === 0 ? 'modx-config-error panel-desc' : ''
                }, {
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
                            fieldLabel  : _('seosuite.tab_meta.keywords'),
                            description : MODx.expandHelp ? '' : _('seosuite.tab_meta.keywords_desc'),
                            anchor      : '100%',
                            name        : 'seosuite_keywords',
                            id          : 'seosuite-keywords',
                            value       : SeoSuite.record.keywords,
                            enableKeyEvents : true,
                            listeners   : {
                                keyup       : {
                                    fn          : function(tf) {
                                        Ext.iterate(this.getFieldKeywordCounters(), (function(key) {
                                            var tf = Ext.getCmp(key);

                                            if (tf) {
                                                this.onUpdateKeywordCounter.call(tf, tf);
                                            }
                                        }).bind(this));
                                    },
                                    scope       : this
                                }
                            }
                        }, {
                            xtype       : MODx.expandHelp ? 'label' : 'hidden',
                            html        : _('seosuite.tab_meta.keywords_desc'),
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
                                cls         : 'x-btn-no-text active',
                                text        : '<i class="icon icon-desktop"></i>',
                                mode        : 'desktop',
                                handler     : this.onChangePreviewMode,
                                scope       : this,
                                listeners   : {
                                    afterrender : {
                                        fn          : function(btn) {
                                            if (btn.mode === SeoSuite.config.meta.preview.mode) {
                                                btn.addClass('x-btn-active');
                                            }
                                        },
                                        scope       : this
                                    }
                                }
                            }, {
                                cls         : 'x-btn-no-text',
                                text        : '<i class="icon icon-mobile"></i>',
                                mode        : 'mobile',
                                handler     : this.onChangePreviewMode,
                                scope       : this,
                                listeners   : {
                                    afterrender : {
                                        fn          : function(btn) {
                                            if (btn.mode === SeoSuite.config.meta.preview.mode) {
                                                btn.addClass('x-btn-active');
                                            }
                                        },
                                        scope       : this
                                    }
                                }
                            }, '->', {
                                xtype       : 'label',
                                html        : _('seosuite.tab_meta.preview')
                            }, {
                                cls         : 'x-btn-no-text active',
                                text        : _('seosuite.tab_meta.preview_google'),
                                engine      : 'google',
                                handler     : this.onChangePreviewEngine,
                                scope       : this,
                                listeners   : {
                                    afterrender : {
                                        fn          : function(btn) {
                                            if (btn.engine === SeoSuite.config.meta.preview.engine) {
                                                btn.addClass('x-btn-active');
                                            }
                                        },
                                        scope       : this
                                    }
                                }
                            }, {
                                cls         : 'x-btn-no-text',
                                text        : _('seosuite.tab_meta.preview_yandex'),
                                engine      : 'yandex',
                                handler     : this.onChangePreviewEngine,
                                scope       : this,
                                listeners   : {
                                    afterrender : {
                                        fn          : function(btn) {
                                            if (btn.engine === SeoSuite.config.meta.preview.engine) {
                                                btn.addClass('x-btn-active');
                                            }
                                        },
                                        scope       : this
                                    }
                                }
                            }]
                        }, {
                            baseCls     : 'seosuite-seo-preview',
                            id          : 'seosuite-seo-preview',
                            cls         : 'seosuite-seo-preview-' + SeoSuite.config.meta.preview.mode + ' seosuite-seo-preview-' + SeoSuite.config.meta.preview.engine,
                            html        : '<img src="https://www.google.com/s2/favicons?domain=test" class="favicon" id="seosuite-seo-preview-favicon" />' +
                                '<div id="seosuite-seo-preview-title"></div>' +
                                '<div id="seosuite-seo-preview-url"></div>' +
                                '<div id="seosuite-seo-preview-description"></div>' +
                                '<div id="seosuite-seo-preview-message">' + _('seosuite.tab_meta.no_preview') + '</div>'
                        }]
                    }]
                }]
            });

            panel.doLayout();

            this.onRenderPreview();
        }
    },
    getFieldCounters: function() {
        var counters = {};

        if (SeoSuite.config.meta.field_counters) {
            Ext.iterate(SeoSuite.config.meta.field_counters, (function(key, length) {
                counters[this.getFieldAlias(key)] = length;
            }).bind(this));
        }

        return counters;
    },
    getFieldKeywordCounters: function() {
        var counters = {};

        if (SeoSuite.config.meta.keywords_field_counters) {
            Ext.iterate(SeoSuite.config.meta.keywords_field_counters, (function(key, maxKeywords) {
                counters[this.getFieldAlias(key)] = maxKeywords;
            }).bind(this));
        }

        return counters;
    },
    getFieldAlias: function(key) {
        if (key === 'longtitle') {
            return 'seosuite-longtitle';
        }

        if (key === 'description') {
            return 'seosuite-description';

        }

        if (key === 'content') {
            return 'ta';
        }

        return 'modx-resource-' + key;
    },
    onAddCounter: function(key, minCounterLength, maxCounterLength) {
        if (minCounterLength !== 0 || maxCounterLength !== 0) {
            var tf = Ext.getCmp(key);

            if (tf) {
                tf.minCounterLength = minCounterLength;
                tf.maxCounterLength = maxCounterLength;

                tf.container.addClass('x-form-seosuite-counter');
                tf.container.createChild({
                    tag     : 'div',
                    class   : 'x-form-seosuite-counter-count ' + (minCounterLength <= 0 ? 'valid' : ''),
                    html    : maxCounterLength
                });
                tf.container.createChild({
                    tag     : 'div',
                    class   : 'x-form-seosuite-counter-progress ' + (minCounterLength <= 0 ? 'valid' : ''),
                    html    : '<span style="width: 0;"></span>'
                });

                tf.on('keyup', this.onUpdateCounter);
                tf.on('change', this.onUpdateCounter);

                this.onUpdateCounter(tf);
            }
        }
    },
    onRefreshCounter: function(tf, length) {
        if (typeof tf !== 'object') {
            tf = Ext.getCmp(tf);
        }

        if (tf) {
            tf.restrictedLength = length;

            this.onUpdateCounter(tf);
        }
    },
    onUpdateCounter: function(tf) {
        if (typeof tf !== 'object') {
            tf = Ext.getCmp(tf);
        }

        if (tf) {
            var minCounterLength = tf.minCounterLength;
            var maxCounterLength = tf.maxCounterLength;

            if (tf.restrictedLength) {
                minCounterLength -= tf.restrictedLength;
                maxCounterLength -= tf.restrictedLength;

                if (minCounterLength < 0) {
                    minCounterLength = 0;
                }

                if (maxCounterLength < 0) {
                    maxCounterLength = 0;
                }
            }

            var count   = Math.round(maxCounterLength - tf.getValue().length).toString();
            var percent = Math.round(tf.getValue().length / (maxCounterLength / 100));
            var state   = 'valid';

            if ((maxCounterLength - tf.getValue().length) < 0) {
                state = 'invalid';
            } else if (tf.getValue().length < minCounterLength) {
                state = 'progress';
            }

            if (percent >= 100) {
                percent = 100;
            }

            tf.container.select('.x-form-seosuite-counter-count').elements.forEach(function(element) {
                var counter = Ext.get(element);

                counter.removeClass('valid').removeClass('invalid').removeClass('progress').addClass(state);

                counter.update(count);
            });

            tf.container.select('.x-form-seosuite-counter-progress').elements.forEach(function(element) {
                var counter = Ext.get(element);

                counter.removeClass('valid').removeClass('invalid').removeClass('progress').addClass(state);

                Ext.get(counter.select('span').elements[0]).setStyle('width', percent + '%');
            });
        }
    },
    onAddKeywordCounter: function(key, maxKeywords) {
        var tf = Ext.getCmp(key);

        if (tf) {
            tf.maxKeywords = maxKeywords;

            tf.container.addClass('x-form-seosuite-keyword-counter x-form-seosuite-keyword-counter__v' + MODx.config.version.split('.')[0]);

            tf.container.createChild({
                tag     : 'div',
                class   : 'x-form-seosuite-keyword-counter-progress',
                html    : _('seosuite.tab_meta.keywords') + ': <span>0</span>'
            });

            tf.on('keyup', this.onUpdateKeywordCounter, tf);
            tf.on('change', this.onUpdateKeywordCounter, tf);

            this.onUpdateKeywordCounter.call(tf, tf);
        }
    },
    onUpdateKeywordCounter: function(tf) {
        var tf = this;

        if (tf) {
            var count       = 0;
            var keywords    = Ext.getCmp('seosuite-keywords');
            var value       = tf.getValue();

            if (tf.originalValue) {
                value       = tf.originalValue;
            }

            if (keywords) {
                keywords.getValue().split(',').forEach(function(keyword) {
                    keyword = keyword.replace(/^\s+/, '').toLowerCase();

                    if (keyword) {
                        var matches = value.toLowerCase().match(new RegExp("(^|[ \s\n\r\t\.,'\(\"\+;!?:\-\>])" + keyword + "($|[ \s\n\r\t.,'\)\"\+!?:;\-\<])", 'gim'));

                        if (matches) {
                            count += matches.length;
                        }
                    }
                });
            }

            tf.container.select('.x-form-seosuite-keyword-counter-progress').elements.forEach(function(element) {
                var counter = Ext.get(element);

                if (tf.maxKeywords > 0) {
                    if (tf.maxKeywords >= count) {
                        counter.removeClass('invalid').addClass('valid');
                    } else {
                        counter.addClass('invalid').removeClass('valid');
                    }
                }

                Ext.get(counter.select('span').elements[0]).update(count.toString());
            });
        }
    },
    onRenderPreview: function () {
        setTimeout((function() {
            MODx.Ajax.request({
                url         : SeoSuite.config.connector_url,
                params      : {
                    action          : 'mgr/resource/preview',
                    id              : Ext.getCmp('modx-resource-id').getValue(),
                    fields          : Ext.encode({
                        pagetitle       : Ext.getCmp('modx-resource-pagetitle').getValue(),
                        longtitle       : Ext.getCmp('modx-resource-longtitle').getValue(),
                        description     : Ext.getCmp('modx-resource-description').getValue(),
                        introtext       : Ext.getCmp('modx-resource-introtext').getValue()
                    }),
                    context_key     : Ext.getCmp('modx-resource-context-key').getValue(),
                    parent          : Ext.getCmp('modx-resource-parent-hidden').getValue(),
                    content_type    : Ext.getCmp('modx-resource-content-type').getValue(),
                    alias           : Ext.getCmp('modx-resource-alias').getValue(),
                    uri             : Ext.getCmp('modx-resource-uri').getValue(),
                    uri_override    : Ext.getCmp('modx-resource-uri-override').getValue(),
                    preview_mode    : this.previewMode || SeoSuite.config.meta.preview.mode,
                    preview_engine  : this.previewEngine || SeoSuite.config.meta.preview.engine
                },
                listeners   : {
                    'success'   : {
                        fn          : function(response) {
                            var preview = Ext.get('seosuite-seo-preview');

                            if (preview && response.results) {
                                var index = Ext.getCmp('seosuite-seo-index');

                                if (index) {
                                    if (parseInt(index.getValue().inputValue) === 0) {
                                        preview.addClass('disabled');
                                    } else {
                                        preview.removeClass('disabled');
                                    }
                                } else {
                                    preview.removeClass('disabled');
                                }

                                preview.select('.favicon').elements.forEach(function(favicon) {
                                    favicon.setAttribute('src', 'https://www.google.com/s2/favicons?domain=' + response.results.output.domain);
                                });

                                var url = [];

                                url.push('<img src="https://www.google.com/s2/favicons?domain=test" class="favicon" />');

                                if (response.results.output.protocol === 'https') {
                                    url.push('<i class="icon icon-lock"></i>');
                                }

                                url.push('<span>' + response.results.output.site_url + '</span>');

                                if (!Ext.isEmpty(response.results.output.alias)) {
                                    response.results.output.alias.split('/').forEach(function(path) {
                                        url.push('<span>' + path + '</span>');
                                    });
                                }

                                Ext.get('seosuite-seo-preview-title').dom.innerHTML = response.results.output.title;
                                Ext.get('seosuite-seo-preview-url').dom.innerHTML   = url.join('');

                                if (!Ext.isEmpty(response.results.output.description)) {
                                    Ext.get('seosuite-seo-preview-description').dom.innerHTML = response.results.output.description;
                                } else {
                                    Ext.get('seosuite-seo-preview-description').dom.innerHTML = _('seosuite.tab_meta.description_empty');
                                }

                                if (!Ext.isEmpty(response.results.output.counters)) {
                                    var counters = this.getFieldCounters();

                                    Ext.iterate(response.results.output.counters, (function(key, length) {
                                        key = this.getFieldAlias(key);

                                        if (counters[key]) {
                                            this.onRefreshCounter(key, length);
                                        }
                                    }).bind(this));
                                }
                            }
                        },
                        scope       : this
                    }
                }
            });
        }).bind(this), 100);
    },
    onChangePreviewMode: function(btn) {
        this.previewMode = btn.mode;

        var preview = Ext.getCmp('seosuite-seo-preview');

        if (preview && btn.ownerCt.items) {
            btn.ownerCt.items.items.forEach(function (item) {
                if (item.mode) {
                    if (item.mode === btn.mode) {
                        item.addClass('x-btn-active');

                        preview.addClass('seosuite-seo-preview-' + item.mode);
                    } else {
                        item.removeClass('x-btn-active');

                        preview.removeClass('seosuite-seo-preview-' + item.mode);
                    }
                }
            });
        }

        this.onRenderPreview();
    },
    onChangePreviewEngine: function(btn) {
        this.previewEngine = btn.engine;

        var preview = Ext.getCmp('seosuite-seo-preview');

        if (preview && btn.ownerCt.items) {
            btn.ownerCt.items.items.forEach(function (item) {
                if (item.engine) {
                    if (item.engine === btn.engine) {
                        item.addClass('x-btn-active');

                        preview.addClass('seosuite-seo-preview-' + item.engine);
                    } else {
                        item.removeClass('x-btn-active');

                        preview.removeClass('seosuite-seo-preview-' + item.engine);
                    }
                }
            });
        }

        this.onRenderPreview();
    }
});

Ext.onReady(function() {
    if (!SeoSuite.config.loaded) {
        SeoSuite.initialize();
    }
});
