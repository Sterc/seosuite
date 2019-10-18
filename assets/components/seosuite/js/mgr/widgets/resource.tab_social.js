Ext.onReady(function() {
    var panel = Ext.getCmp('modx-resource-tabs');

    if (panel) {
        panel.add({
            title       : _('seosuite.tab_social'),
            items       : [{
                xtype       : 'modx-vtabs',
                items       : [{
                    title       : '<i class="icon icon-facebook"></i>' + _('seosuite.tab_social.tab_facebook')
                }, {
                    title       : '<i class="icon icon-twitter"></i>' + _('seosuite.tab_social.tab_twitter')
                }]
            }]
        });
    }
});