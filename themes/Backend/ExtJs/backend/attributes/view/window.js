
//{namespace name="backend/attributes/main"}

Ext.define('Shopware.apps.Attributes.view.Window', {
    extend: 'Enlight.app.Window',
    alias: 'widget.attributes-window',
    cls: 'attributes-detail-window',

    layout: {
        type: 'hbox',
        align: 'stretch'
    },

    title: '{s name="window_title"}{/s}',

    width: '80%',
    height: '80%',

    initComponent: function() {
        var me = this;
        me.items = me.createItems();
        me.callParent(arguments);
    },

    createItems: function() {
        var me = this;

        me.listingStore = Ext.create('Shopware.apps.Attributes.store.Column');

        me.listing = Ext.create('Shopware.apps.Attributes.view.Listing', {
            region: 'center',
            store: me.listingStore,
            flex: 1,
            table: me.table
        });

        return [
            me.listing,
            me.createDetailForm()
        ];
    },

    createDetailForm: function() {
        var me = this;

        me.detail = Ext.create('Shopware.apps.Attributes.view.Detail', {
            flex: 1
        });

        me.detailForm = Ext.create('Ext.form.Panel', {
            items: [me.detail],
            region: 'east',
            disabled: true,
            bodyPadding: 20,
            cls: 'shopware-form',
            layout: { type: 'hbox', align: 'stretch' },
            width: 600,
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'bottom',
                items: [
                '->',
                /*{if {acl_is_allowed privilege=update}}*/
                {
                    xtype: 'button',
                    cls: 'primary',
                    text: '{s name="save_button"}{/s}',
                    handler: function() {
                        me.fireEvent('save-column', me.detailForm);
                    }
                }
                /*{/if}*/
                ]
            }]
        });

        return me.detailForm;
    }
});