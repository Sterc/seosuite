var Buster404 = function(config) {
    config = config || {};
Buster404.superclass.constructor.call(this,config);
};
Ext.extend(Buster404,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('buster404',Buster404);
Buster404 = new Buster404();