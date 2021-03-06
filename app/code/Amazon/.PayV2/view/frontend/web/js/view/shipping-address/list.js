define([
    'Magento_Checkout/js/view/shipping-address/list',
    'Magento_Customer/js/model/address-list',
    'Amazon_PayV2/js/model/storage',
    'ko'
], function (Component, addressList, amazonStorage, ko) {
    'use strict';

    if (!amazonStorage.isAmazonCheckout()) {
        return Component;
    }

    return Component.extend({
        /**
         * Init address list
         */
        initObservable: function () {
            this._super();
            this.visible = true;
            return this;
        }
    });
});
