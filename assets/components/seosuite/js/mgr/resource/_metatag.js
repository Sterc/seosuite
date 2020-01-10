SeoSuite.panel.MetaTag = function(config) {
    config = config || {};

    SeoSuite.currentCaretPosition = [];

    Ext.applyIf(config, {
        layout          : 'form',
        labelSeparator  : '',
        items           : [{
            xtype           : 'button',
            id              : 'seosuite-preview-insert-' + config.id,
            text            : '<i class="icon icon-plus"></i> ' + _('seosuite.tab_meta.add_field'),
            handler         : function (btn) {
                var type = 'description';
                if (btn.target_field === 'seosuite-variables-preview-title') {
                    type = 'title';
                }

                /* Set default position if needed. */
                if (!SeoSuite.currentCaretPosition[type]) {
                    SeoSuite.currentCaretPosition[type]             = [];
                    SeoSuite.currentCaretPosition[type]['element']  = Ext.query('#' + btn.target_field)[0];
                    SeoSuite.currentCaretPosition[type]['position'] = 0;
                }

                this.showVariableWindow(btn);
            },
            scope           : this,
            target_field    : 'seosuite-variables-preview-' + config.id,
        }, {
            xtype           : 'textfield',
            fieldLabel      : config.label,
            name            : config.name,
            description     : MODx.expandHelp ? '' : config.description,
            id              : 'seosuite-preview-editor-' + config.id,
            anchor          : '100%',
            value           : config.value,
            hidden          : true,
            listeners       : {
                change          : {
                    fn              : this.onUpdateValue,
                    scope           : this
                }
            }
        }, {
            fieldLabel  : config.label,
            html        : '<div id="seosuite-variables-preview-' + config.id + '"></div>',
            listeners   : {
                afterRender: function () {
                    Ext.get('seosuite-variables-preview-' + config.id).dom.innerHTML = this.renderVariablesPreview(config.value);

                    var self = this;
                    document.getElementById('seosuite-variables-preview-' + config.id).addEventListener('input', function(event) {
                        SeoSuite.currentCaretPosition            = [];
                        SeoSuite.currentCaretPosition[config.id] = self.getCaretPosition();

                        var parent = self.getSelectionBoundaryElement('start');
                        if (parent.tagName === 'DIV') {
                            var parts = [];
                            event.target.innerHTML.split(/<\/span>/gm).forEach(function (value) {
                                /* If string does not start with span, prepend it with a span element. */
                                if (!value.startsWith('<span')) {
                                    /* If the string does include a span, make sure we close the span before. */
                                    if (value.includes('<span')) {
                                        value = value.replace('<span', '</span><span');
                                    }

                                    value = '<span id="' + SeoSuite.generateUniqueID() + '">' + value;
                                }

                                value += '</span>';

                                parts.push(value);
                            });

                            var position = self.getCaretPosition(document.getElementById('seosuite-variables-preview-' + config.id))['position'];

                            event.target.innerHTML = parts.join('');

                            self.setCaretPosition('seosuite-variables-preview-' + config.id, position);
                        }

                        SeoSuite.currentCaretPosition            = [];
                        SeoSuite.currentCaretPosition[config.id] = self.getCaretPosition();

                        SeoSuite.updateHiddenMetafieldValue(config.id);
                    }, false);

                    var elements = document.querySelectorAll('.seosuite-snippet-variable');
                    for (i = 0; i < elements.length; ++i) {
                        elements[i].addEventListener('click', function (event) {
                            SeoSuite.selectElementById(event.target.id);
                        });
                    }

                    document.getElementById('seosuite-variables-preview-' + config.id).addEventListener('focusin', function() {
                        /* Make sure to reset current caret position, in case other field is focused upon. */
                        SeoSuite.currentCaretPosition            = [];
                        SeoSuite.currentCaretPosition[config.id] = self.getCaretPosition();
                    });

                    document.getElementById('seosuite-variables-preview-' + config.id).addEventListener('click', function() {
                        /* Make sure to reset current caret position, in case other field is focused upon. */
                        SeoSuite.currentCaretPosition            = [];
                        SeoSuite.currentCaretPosition[config.id] = self.getCaretPosition();
                    });

                    document.getElementById('seosuite-variables-preview-' + config.id).addEventListener('keyup', function(event) {
                        /* If left arrow or right arrow. */
                        if (event.keyCode === 37 || event.keyCode === 39) {
                            var selection = window.getSelection();

                            /* If arrow cursor is moved by arrow key and cursor is inside seosuite snippet variable, then auto select the element. */
                            if (selection.anchorNode.parentNode.classList.contains('seosuite-snippet-variable') && selection.type !== 'Range') {

                                /* Ignore if already selected. */
                                console.log(event);
                                SeoSuite.selectElementById(selection.anchorNode.parentNode.id);
                            }

                            SeoSuite.currentCaretPosition            = [];
                            SeoSuite.currentCaretPosition[config.id] = self.getCaretPosition();
                        }
                    });
                },
                scope: this
            }
        }, {
            xtype: MODx.expandHelp ? 'label' : 'hidden',
            html : config.description,
            cls  : 'desc-under'
        }]
    });

    SeoSuite.panel.MetaTag.superclass.constructor.call(this, config);

    this.addEvents('change');
};

Ext.extend(SeoSuite.panel.MetaTag, MODx.Panel, {
    onUpdateValue: function(tf) {
        this.fireEvent('change', this, tf.getValue());
    },
    getSelectionBoundaryElement: function (isStart) {
        var range, sel, container;
        if (document.selection) {
            range = document.selection.createRange();
            range.collapse(isStart);
            return range.parentElement();
        } else {
            sel = window.getSelection();
            if (sel.getRangeAt) {
                if (sel.rangeCount > 0) {
                    range = sel.getRangeAt(0);
                }
            } else {
                // Old WebKit
                range = document.createRange();
                range.setStart(sel.anchorNode, sel.anchorOffset);
                range.setEnd(sel.focusNode, sel.focusOffset);

                // Handle the case when the selection was selected backwards (from the end to the start in the document)
                if (range.collapsed !== sel.isCollapsed) {
                    range.setStart(sel.focusNode, sel.focusOffset);
                    range.setEnd(sel.anchorNode, sel.anchorOffset);
                }
            }

            if (range) {
                container = range[isStart ? "startContainer" : "endContainer"];

                /* Check if the container is a text node and return its parent if so. */
                return container.nodeType === 3 ? container.parentNode : container;
            }
        }
    },
    getCaretPosition: function (element) {
        if (typeof element === 'undefined') {
            /* Set element to span element. */
            var element = this.getSelectionBoundaryElement();
        }

        var caretOffset = 0;
        var doc         = element.ownerDocument || element.document;
        var win         = doc.defaultView || doc.parentWindow;
        var sel;

        if (typeof win.getSelection != "undefined") {
            sel = win.getSelection();

            if (sel.rangeCount > 0) {
                var range         = win.getSelection().getRangeAt(0);
                var preCaretRange = range.cloneRange();

                preCaretRange.selectNodeContents(element);
                preCaretRange.setEnd(range.endContainer, range.endOffset);

                caretOffset = preCaretRange.toString().length;
            }
        } else if ( (sel = doc.selection) && sel.type != "Control") {
            var textRange         = sel.createRange();
            var preCaretTextRange = doc.body.createTextRange();

            preCaretTextRange.moveToElementText(element);
            preCaretTextRange.setEndPoint("EndToEnd", textRange);

            caretOffset = preCaretTextRange.text.length;
        }

        return {
            element : this.getSelectionBoundaryElement('start'),
            position: caretOffset
        };
    },
    createRange: function (node, chars, range) {
        if (!range) {
            range = document.createRange()
            range.selectNode(node);
            range.setStart(node, 0);
        }

        if (chars.count === 0) {
            range.setEnd(node, chars.count);
        } else if (node && chars.count > 0) {
            if (node.nodeType === Node.TEXT_NODE) {
                if (node.textContent.length < chars.count) {
                    chars.count -= node.textContent.length;
                } else {
                    range.setEnd(node, chars.count);
                    chars.count = 0;
                }
            } else {
                for (var lp = 0; lp < node.childNodes.length; lp++) {
                    range = this.createRange(node.childNodes[lp], chars, range);

                    if (chars.count === 0) {
                        break;
                    }
                }
            }
        }

        return range;
    },
    setCaretPosition: function (elemId, pos) {
        var selection = window.getSelection();

        var range = this.createRange(document.getElementById(elemId), { count: pos });
        if (range) {
            range.collapse(false);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    },
    node_walk: function (node, func) {
        var result = func(node);

        for (node = node.firstChild; result !== false && node; node = node.nextSibling) {
            result = this.node_walk(node, func);
        }

        return result;
    },
    renderVariablesPreview: function (json) {
        var output = '<div class="x-form-text" contenteditable="true">';

        if (json && json.length > 0) {
            var jsonObject = JSON.parse(json);

            for (var property in jsonObject) {
                if (jsonObject.hasOwnProperty(property)) {
                    var uniqueID = SeoSuite.generateUniqueID();

                    if (jsonObject[property].type === 'placeholder') {
                        output += '<span class="seosuite-snippet-variable" spellcheck="false" id="' + uniqueID + '">' + jsonObject[property].value + '</span>';
                    } else {
                        output += '<span id="' + uniqueID + '">' + jsonObject[property].value + '</span>';
                    }
                }
            }
        }

        output += '</div>';

        return output;
    },
    showVariableWindow: function(btn) {
        var record = {
            target_field: btn.target_field
        };

        if (this.variablesWindow) {
            this.variablesWindow.destroy();
        }

        this.variablesWindow = MODx.load({
            xtype       : 'seosuite-window-insertvariable',
            record      : record,
            closeAction : 'close'
        });

        this.variablesWindow.setValues(record);
        this.variablesWindow.show();
    }
});

Ext.reg('seosuite-field-metatag', SeoSuite.panel.MetaTag);

SeoSuite.combo.SnippetVariables = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        name            : 'variable',
        hiddenName      : 'variable',
        displayField    : 'value',
        id              : 'seosuite-variable',
        valueField      : 'key',
        fields          : ['key', 'value'],
        pageSize        : 20,
        url             : SeoSuite.config.connector_url,
        baseParams      : {
            action: 'mgr/resource/variables/getlist'
        },
        typeAhead       : false,
        editable        : false
    });

    SeoSuite.combo.SnippetVariables.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.combo.SnippetVariables, MODx.combo.ComboBox);
Ext.reg('seosuite-combo-snippet-variables', SeoSuite.combo.SnippetVariables);

SeoSuite.window.InsertVariable = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        autoHeight  : true,
        title       : _('seosuite.tab_meta.insert_field'),
        fields      : [{
            xtype       : 'hidden',
            name        : 'target_field'
        }, {
            xtype       : 'seosuite-combo-snippet-variables',
            fieldLabel  : _('seosuite.tab_meta.field'),
            description     : MODx.expandHelp ? '' : _('seosuite.tab_meta.field_desc'),
            hiddenName  : 'variables',
            anchor      : '100%',
            allowBlank  : false
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.tab_meta.field_desc'),
            cls         : 'desc-under'
        }],
        buttons: [{
            text    : _('seosuite.tab_meta.insert_btn'),
            handler :  function() {
                var variable     = Ext.getCmp('seosuite-variable').getValue();
                var preview      = Ext.get(config.record.target_field).query('.x-form-text')[0];
                var html         = preview.innerHTML;
                var uniqueID = SeoSuite.generateUniqueID();
                var variableHTML = '<span class="seosuite-snippet-variable" spellcheck="false" id="' + uniqueID + '">' + variable + '</span>';

                var type = 'description';
                if (config.record.target_field === 'seosuite-variables-preview-title') {
                    type = 'title';
                }

                /* If tagname is DIV insert HTML at the start or end of string. */
                if (SeoSuite.currentCaretPosition[type]['element'].tagName === 'DIV') {
                    if (SeoSuite.currentCaretPosition[type]['position'] === 0) {
                        html = variableHTML + html;
                    } else {
                        html += variableHTML;
                    }
                } else {
                    /**
                     * Because now the variable is inserted in existing span, first close the existing span.
                     */
                    variableHTML = '</span>' + variableHTML;

                    /* Set oldHTML which will replace later on. */
                    element     = SeoSuite.currentCaretPosition[type]['element'];
                    var oldHTML = SeoSuite.currentCaretPosition[type]['element'].outerHTML;

                    element.innerHTML = element.innerHTML.replace(/&nbsp;/g, ' ');

                    /* Set rawHTML, which will exist out of the innerHTML with the new variable inserted. */
                    var rawHTML = this.insertStringAtPosition(element.innerHTML, variableHTML, SeoSuite.currentCaretPosition[type]['position']);

                    /* Prepend rawHTML again with original span ID. */
                    rawHTML = '<span id="' + element.id + '">' + rawHTML;

                    /* Retrieve final part of string which is now not wrapped in a span anymore. */
                    var parts = [];
                    rawHTML.split(/<\/span>/gm).forEach(function(value) {
                        parts.push(value);
                    });

                    var lastValue = parts.slice(-1)[0];

                    /* Remove final unwrapped part first. */
                    rawHTML = rawHTML.substring(0, rawHTML.length - lastValue.length);

                    /* Now readd the final unwrapped part of the string and wrap it in a span. */
                    rawHTML += '<span id="' + SeoSuite.generateUniqueID() + '">' + lastValue + '</span>';

                    html = html.replace(oldHTML.toString(), rawHTML);
                }

                /* Updates the rendered variable preview. */
                preview.innerHTML = html;

                SeoSuite.updateHiddenMetafieldValue(type);

                var elements = document.querySelectorAll('.seosuite-snippet-variable');
                for (i = 0; i < elements.length; ++i) {
                    elements[i].addEventListener('click', function (event) {
                        SeoSuite.selectElementById(event.target.id);
                    });
                }

                this.destroy();
            },
            scope: this
        }]
    });

    SeoSuite.window.InsertVariable.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.window.InsertVariable, MODx.Window, {
    insertStringAtPosition: function (main_string, ins_string, pos) {
        return pos > 0
            ? main_string.replace(new RegExp('.{' + pos + '}'), '$&' + ins_string)
            : ins_string + main_string;
    }
});

Ext.reg('seosuite-window-insertvariable', SeoSuite.window.InsertVariable);
