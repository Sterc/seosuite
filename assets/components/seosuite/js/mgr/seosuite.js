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
    config  : {}
});

Ext.reg('seosuite', SeoSuite);

SeoSuite = new SeoSuite();
