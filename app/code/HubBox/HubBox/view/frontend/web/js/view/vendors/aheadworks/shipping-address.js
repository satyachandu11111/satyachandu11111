define(
    [
        'jquery',
        'underscore',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',

        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
        'Aheadworks_OneStepCheckout/js/model/shipping-address/new-address-form-state',

        'HubBox_HubBox/js/model/hubbox',
        'HubBox_HubBox/js/model/checkout-data',
        'HubBox_HubBox/js/view/helper'

    ],function (
        $,
        _,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        checkoutData,
        registry,

        sameAsShippingFlag,
        newAddressFormState,

        hubBox,
        hbCheckoutData,
        hubBoxHelper
    ) {
        'use strict';

        var mixin = {
            defaults: {
                template: 'HubBox_HubBox/vendors/aheadworks/shipping-address'
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                this._super();

                this.isShown = ko.computed(function () {
                    return !quote.isQuoteVirtual();
                });
                this.showForm = ko.computed(function () {
                    var addressOptions = addressList().filter(function (address) {
                        return address.getType() !== 'hubbox-address';
                    });

                    return addressOptions.length == 0;
                });
                this.showNewAddressFormHeader = ko.computed(function () {
                    return !this.isNewAddressAdded() || newAddressFormState.isShown();
                }, this);

                return this;
            },

            /**
             * @return {exports.initObservable}
             */
            initialize: function () {
                this._super();
                hubBoxHelper.boot();
                return this;
            }
        };

        return function (target) {
            // ensure hubbox is on and order is collectable.
            if (window.checkoutConfig.hubBox && window.checkoutConfig.hubBox.isClickAndCollectable) {
                mixin = $.extend({}, hubBoxHelper, mixin);
                return target.extend(mixin); // new result that all other modules receive
            } else {
                if (hbCheckoutData.getIsHubBoxOrder()) {
                    hubBoxHelper.unConfirmAndUnsetHubBox();
                }
                return target;
            }
        }
    });
