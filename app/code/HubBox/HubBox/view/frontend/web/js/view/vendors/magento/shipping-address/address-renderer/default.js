
define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote'
], function(
    $,
    ko,
    quote
) {
    'use strict';

    var mixin = {
        defaults: {
            template: 'HubBox_HubBox/vendors/magento/shipping-address/address-renderer/default',

            /** @inheritdoc */
            initObservable: function () {
                this._super();

                this.isSelected = ko.computed(function () {
                    var isSelected = false,
                        shippingAddress = quote.shippingAddress();
                    if (shippingAddress) {
                        isSelected = shippingAddress.getKey() == this.address().getKey(); //eslint-disable-line eqeqeq
                    }
                    return isSelected;
                }, this);

                this.notHubBoxAddress = ko.computed(function() {
                    return (this.address().customAttributes === undefined
                        || this.address().customAttributes.isHubBoxAddress === undefined
                        || this.address().customAttributes.isHubBoxAddress === false);
                }, this);

                return this;
            }
        }
    };

    return function (target) {
        return target.extend(mixin);
    }
});
