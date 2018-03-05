define([
    'Magento_Ui/js/form/components/insert-form',
    'uiRegistry'
], function (InsertForm, registry) {
    'use strict';

    return InsertForm.extend({
        initialize: function() {
            this._super();

            this.params.menu_id   = registry.get(this.provider).data.menu_id;
            this.params.parent_id = registry.get(this.provider).data.item_id;

            return this;
        }
    });
});
