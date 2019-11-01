var SeoSuite = function(config) {
    config = config || {};

    SeoSuite.superclass.constructor.call(this, config);
};

Ext.extend(SeoSuite, Ext.Component, {
    page    : {},
    window  : {},
    grid    : {},
    tree    : {},
    panel   : {},
    combo   : {},
    config  : {},
    generateUniqueID: function () {
        var dt   = new Date().getTime();
        var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = (dt + Math.random()*16)%16 | 0;

            dt = Math.floor(dt/16);

            return (c == 'x' ? r : (r&0x3|0x8)).toString(16);
        });

        return uuid;
    },
    updateHiddenMetafieldValue: function (id) {
        var json = [];
        var html = Ext.get('seosuite-variables-preview-' + id).query('.x-form-text')[0].innerHTML;
        if (html.length > 0) {
            html.split(/<\/span>/gm).forEach(function(value) {
                /* Make sure every text is wrapped inside a span tag. */
                if (!value.startsWith('<span')) {
                    value = '<span>' + value;
                }

                value += '</span>';

                /* Return the text between the span tags. */
                var text = value.replace(/<[^>]+>/g, '');
                if (text.length > 0) {
                    if (value.includes('seosuite-snippet-variable')) {
                        json.push({
                            type : 'placeholder',
                            value: text
                        });
                    } else {
                        json.push({
                            type : 'text',
                            value: text
                        });
                    }
                }
            });
        }

        var box = Ext.getCmp('seosuite-preview-editor-' + id);

        box.setValue(JSON.stringify(json));
        box.fireEvent('change', box);
    }
});

Ext.reg('seosuite', SeoSuite);

SeoSuite = new SeoSuite();
