SeoSuite.panel.MetaTag = function(config) {
    config = config || {};

    SeoSuite.currentCaretPosition = [];

    Ext.applyIf(config, {
        itemCls     : 'seosuite-meta-field',
        items       : [{
            xtype       : 'hidden',
            name        : config.name,
            id          : 'seosuite-preview-editor-' + config.id,
            value       : config.value,
            listeners   : {
                change      : {
                    fn          : this.onUpdateValue,
                    scope       : this
                }
            }
        }, {
            xtype       : 'button',
            cls         : 'seosuite-meta-field-btn',
            text        : '<i class="icon icon-plus"></i> ' + _('seosuite.tab_meta.add_variable'),
            handler     : this.insertVariable,
            scope       : this,
        }, {
            cls         : 'seosuite-meta-editor',
            html        : '<span class="x-form-text" id="seosuite-meta-editor-' + config.id + '" contenteditable="true" spellcheck="false"></span>',
            listeners   : {
                afterrender : {
                    fn          : this.onAfterRender,
                    scope       : this
                }
            }
        }]
    });

    SeoSuite.panel.MetaTag.superclass.constructor.call(this, config);

    this.addEvents('change');
};

Ext.extend(SeoSuite.panel.MetaTag, MODx.Panel, {
    onAfterRender: function() {
        this.editor = Ext.get('seosuite-meta-editor-' + this.id);
        this.range  = null;

        this.editor.addListener('keydown', (function(event, tf) {
            // If key is backspace or delete do nothing.
            //if (event.keyCode === 8 || event.keyCode === 46) {

            //}

            // If key is backspace or delete do nothing.
            //if (event.keyCode === 8 || event.keyCode === 46) {
            //    var node = window.getSelection().getRangeAt(0).startContainer.parentNode;

            //    if (node.getAttribute('data-type') === 'variable') {
            //        event.preventDefault();
            //    }
            //}
            //this.onEditorKeyDown(event, this.selection);
        }).bind(this));

        this.editor.addListener('keyup', (function(event, tf) {
            // If key is arrow left or right update selection.
            //if (event.keyCode === 37 || event.keyCode === 39) {
            //    this.onEditorUpdateCaret(event, 'key');
            //}
        }).bind(this));

        this.editor.addListener('keydown', (function(event, tf) {
            this.onEditorUpdate(event);
        }).bind(this));

        this.editor.addListener('keyup', (function(event, tf) {
            this.onEditorUpdateNode(event, 'key');
        }).bind(this));

        this.editor.addListener('click', (function(event, tf) {
            this.onEditorUpdateNode(event, 'click');
        }).bind(this));

        this.editor.addListener('blur', (function(event, tf) {
            this.onEditorUpdateNode(event, 'blur');
            this.onEditorUpdateHiddenValue(event);
        }).bind(this));

        if (this.value) {
            this.onEditorParseHiddenValue(this.value);
        }
    },
    setValue: function(value) {
        this.onEditorParseHiddenValue(value);
        this.onEditorUpdateHiddenValue();
    },
    getValue: function() {
        var hiddenField = Ext.getCmp('seosuite-preview-editor-' + this.id);

        if (hiddenField) {
            return hiddenField.getValue();
        }

        return '';
    },
    onEditorUpdate: function(event) {
        console.log('onEditorUpdate', event.browserEvent, event.browserEvent.key, event.browserEvent.key.length);

        //Enter = 13
        //Backspace = 8
        //Delete = 49
        if (event.browserEvent.key === 'Enter') {
            event.preventDefault();
        } else if (event.browserEvent.key === 'Delete' || event.browserEvent.key === 'Backspace') {
            console.log('DELETE');

            if (window.getSelection()) {
                var range = window.getSelection().getRangeAt(0);

                console.log(range);

                if (this.isVariableNode(range.commonAncestorContainer.parentNode)) {
                    range.setStartBefore(range.commonAncestorContainer.parentNode.childNodes[0]);
                    range.setEndAfter(range.commonAncestorContainer.parentNode.childNodes[0]);

                    console.log('JA');
                    //var newRange = range.cloneRange();

                    //newRange.setStartBefore(range.startContainer.parentNode.childNodes[0]);
                    //newRange.setEndAfter(range.startContainer.parentNode.childNodes[0]);

                    //selection.removeAllRanges();
                    //selection.addRange(newRange);
                }
            }
        } else if (event.browserEvent.key.length === 1 && !event.browserEvent.ctrlKey) {
            if (window.getSelection()) {
                var range       = window.getSelection().getRangeAt(0);
                var newRange    = range.cloneRange();

                /**
                 * Check if the node is a variable.
                 * If yes, then add the text to a new node before or after the variable node.
                 */
                if (this.isVariableNode(range.startContainer.parentNode)) {
                    var sibling = null;

                    if (range.startOffset === 0) {
                        sibling = range.startContainer.parentNode.previousSibling;

                        if (!this.isVariableNode(sibling)) {
                            if (sibling = this.getVariableNode(event.browserEvent.key)) {
                                this.editor.dom.insertBefore(sibling, range.startContainer.parentNode);
                            }
                        }

                        newRange.setStart(sibling.childNodes[0], 0);
                        newRange.setEnd(sibling.childNodes[0], 1);
                    }

                    if (range.startOffset === range.startContainer.length) {
                        sibling = range.startContainer.parentNode.nextSibling;

                        if (!this.isVariableNode(sibling)) {
                            if (sibling = this.getVariableNode(event.browserEvent.key)) {
                                this.editor.dom.insertBefore(sibling, range.startContainer.parentNode.nextSibling);
                            }
                        }

                        newRange.setStart(sibling.childNodes[0], 0);
                        newRange.setEnd(sibling.childNodes[0], 1);
                    }

                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(newRange);
                }
            }
        }
    },
    onEditorUpdateNode: function(event, method) {
        if (window.getSelection()) {
            var range       = window.getSelection().getRangeAt(0);
            var newRange    = range.cloneRange();

            /**
             * Check if method is a key.
             * If yes, check if the node is a variable and set the range to the begin and start of the node if the key is an arrow.
             */
            if (method === 'key') {
                if (this.isVariableNode(range.startContainer.parentNode)) {
                    if (event.browserEvent.key === 'ArrowRight') {
                        if (range.startOffset < range.startContainer.length) {
                            newRange.setStartBefore(range.startContainer.parentNode.childNodes[0]);
                            newRange.setEndAfter(range.startContainer.parentNode.childNodes[0]);
                        }
                    }

                    if (event.browserEvent.key === 'ArrowLeft') {
                        if (range.startOffset > 0) {
                            newRange.setStartBefore(range.startContainer.parentNode.childNodes[0]);
                            newRange.setEndAfter(range.startContainer.parentNode.childNodes[0]);
                        }
                    }
                }
            }

            /**
             * Check if method is click.
             * If yes, check if the start node is a variable and set the range to the begin of the node.
             * If yes, check if the end node is a variable and set the range to the end of the node.
             */
            if (method === 'click') {
                if (this.isVariableNode(range.startContainer.parentNode)) {
                    newRange.setStartBefore(range.startContainer.parentNode.childNodes[0]);
                }

                if (this.isVariableNode(range.endContainer.parentNode)) {
                    newRange.setEndAfter(range.endContainer.parentNode.childNodes[0]);
                }
            }

            if (method === 'blur') {
                this.range = newRange;
            } else {
                window.getSelection().removeAllRanges();

                window.getSelection().addRange(newRange);
            }
        }
    },
    onEditorInsertNode: function(variable) {
        if (this.range) {
            var newRange    = this.range.cloneRange();

            if (newRange.startContainer === newRange.endContainer) {
                console.log('ZELFDE');
            } else {
                console.log('NIET ZELFDE');
            }

            //var newNode = document.createTextNode(variable);

            //newRange.deleteContents();

            //newRange.insertNode(newNode);
            //newRange.selectNodeContents(newNode);


            //console.log(newRange.toString());
            //var newNode     = this.getVariableNode(variable);

            //newNode.setAttribute('data-type', 'variable');

            //newRange.deleteContents();

            //newRange.insertNode(newNode);
            //newRange.selectNode(newNode);

            //window.getSelection().removeAllRanges();
            //window.getSelection().addRange(newRange);

            this.range = newRange;
        }
    },
    isVariableNode: function(node) {
        return node && node.getAttribute('data-type') === 'variable';
    },
    getVariableNode: function(value) {
        var element = document.createElement('span');

        if (element) {
            element.appendChild(document.createTextNode(value || 'a'));
        }

        return element;
    },
    onEditorParseHiddenValue: function(value) {
        var hiddenField = Ext.getCmp('seosuite-preview-editor-' + this.id);

        if (hiddenField) {
            var data = [];

            if (!Ext.isEmpty(value)) {
                var json = Ext.decode(value);

                if (json) {
                    json.forEach(function(item, index) {
                        var nextItem = null;

                        if (json[index + 1]) {
                            nextItem = json[index + 1];
                        }

                        if (item.type === 'variable') {
                            data.push('<span data-type="variable">' + (item.value || '') + '</span>');

                            if (nextItem && nextItem.type === 'variable') {
                                data.push('<span> </span>');
                            }
                        } else {
                            data.push('<span>' + (item.value || '') + '</span>');
                        }
                    });
                }
            }

            this.editor.dom.innerHTML = data.join('');
        }
    },
    onEditorUpdateHiddenValue: function() {
        var hiddenField = Ext.getCmp('seosuite-preview-editor-' + this.id);

        if (hiddenField) {
            var data    = [];
            var matches = this.editor.dom.innerHTML.replace(/&nbsp;/g, ' ').match(/(<span.*?>(.*?)<\/span>|([^<]+))/gm);

            if (matches) {
                matches.forEach(function (value) {
                    var string = value.match(/<span>(.*?)<\/span>/);
                    var variable = value.match(/<span\sdata-type="([a-z]+)">(.*?)<\/span>/);

                    if (string) {
                        if (!Ext.isEmpty(string[1])) {
                            data.push({
                                type    : 'text',
                                value   : string[1]
                            });
                        }
                    } else if (variable) {
                        if (!Ext.isEmpty(variable[2])) {
                            data.push({
                                type    : 'variable',
                                value   : variable[2]
                            });
                        }
                    }
                });
            }

            hiddenField.setValue(Ext.encode(data));
            hiddenField.fireEvent('change', this);
        }
    },
    insertVariable: function(btn) {
        if (this.insertVariableWindow) {
            this.insertVariableWindow.destroy();
        }

        this.insertVariableWindow = MODx.load({
            xtype       : 'seosuite-window-insert-variable',
            closeAction : 'close',
            listeners   : {
                submit      : {
                    fn          : function(record) {
                        this.onEditorInsertNode(record.variable);
                    },
                    scope       : this
                }
            }
        });

        this.insertVariableWindow.show();
    },
    onUpdateValue: function(tf) {
        console.log('onUpdateValue');
        this.fireEvent('change', this, tf.getValue());
    },




    onEditorUpdateCaret: function (event, type) {
        var selection   = window.getSelection();
        var range       = selection.getRangeAt(0);

        var node        = event.browserEvent.target;

        if (type === 'click') {
            node = event.browserEvent.target;
        } else {
            node = range.startContainer.parentNode;
        }

        console.log('onEditorUpdateCaret', range.startContainer.parentNode, range.endContainer.parentNode);

        //var position        = this.getEditorCaretPosition(range, node);
        var position        = range.startOffset;
        var positionOffset  = range.endOffset - range.startOffset;
        var positionType    = '';
        var nodeEditable    = this.getEditorElementEditable(node);
        var nodeLength      = node.textContent.length;

        if (position === 0) {
            positionType = 'prev';
        } else if (position === nodeLength) {
            positionType = 'next';
        }

        //if (!this.getEditorElementEditable(range.startContainer.parentNode)) {
        //    range.setStart(range.startContainer.parentNode, 0);
        //}

        //if (!this.getEditorElementEditable(range.endContainer.parentNode)) {
        //    range.setEnd(range.endContainer.parentNode, 1);
        //}

        this.selection = {
            position        : position,
            positionOffset  : positionOffset,
            positionType    : positionType,
            node            : node,
            nodeEditable    : nodeEditable
        };

        console.log(this.selection);
    },
    getEditorElement: function(value) {
        var element = document.createElement('span');

        if (element) {
            element.appendChild(document.createTextNode(value || 'n'));
        }

        return element;
    },
    getEditorElementEditable: function(node) {
        return node.getAttribute('data-type') !== 'variable';
    },
    getEditorCaretPosition: function (range, node) {
        if (range && node) {
            var caret = range.cloneRange();

            if (caret) {
                caret.selectNodeContents(node);

                caret.setEnd(range.endContainer, range.endOffset);

                return caret.toString().length;
            }
        }

        return 0;
    }
});

Ext.reg('seosuite-field-metatag', SeoSuite.panel.MetaTag);

SeoSuite.window.InsertVariable = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        autoHeight  : true,
        title       : _('seosuite.tab_meta.insert_variable'),
        fields      : [{
            xtype       : 'seosuite-combo-snippet-variable',
            fieldLabel  : _('seosuite.tab_meta.label_variable'),
            description : MODx.expandHelp ? '' : _('seosuite.tab_meta.label_variable_desc'),
            anchor      : '100%',
            allowBlank  : false
        }, {
            xtype       : MODx.expandHelp ? 'label' : 'hidden',
            html        : _('seosuite.tab_meta.label_variable_desc'),
            cls         : 'desc-under'
        }],
        buttons     : [{
            text        : _('seosuite.tab_meta.submit'),
            handler     : this.submit,
            scope       : this
        }]
    });

    SeoSuite.window.InsertVariable.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite.window.InsertVariable, MODx.Window, {
    submit: function() {
        var form = this.fp.getForm();

        if (form.isValid()) {
            this.fireEvent('submit', form.getValues());

            if (close) {
                if (this.config.closeAction !== 'close') {
                    this.hide();
                } else {
                    this.close();
                }
            }
        }
    },
});

Ext.reg('seosuite-window-insert-variable', SeoSuite.window.InsertVariable);
