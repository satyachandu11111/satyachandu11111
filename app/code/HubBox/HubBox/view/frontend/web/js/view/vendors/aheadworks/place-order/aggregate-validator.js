define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',

        'uiRegistry',
        'HubBox_HubBox/js/model/hubbox'

    ],function (
        $,
        _,
        Component,
        ko,

        registry,
        hubBox
    ) {
        'use strict';

        var mixin = {

            /**
             * Validate addresses data
             *
             * @returns {Boolean}
             */
            _validateAddresses: function () {
                var isValid = true,
                    provider = registry.get('checkoutProvider');

                var registryArr = ['checkout.shippingAddress', 'checkout.paymentMethod.billingAddress'];

                if (hubBox.collectPointConfirmed()) {
                    registryArr = ['checkout.paymentMethod.billingAddress'];
                }

                _.each(registryArr, function (query) {
                    var addressComponent = registry.get(query);

                    addressComponent.validate();
                    if (provider.get('params.invalid')) {
                        isValid = false;
                    }
                }, this);

                return isValid;
            },

            /**
             * Validate shipping method
             *
             * @returns {boolean}
             */
            _validateShippingMethod: function () {

                if (hubBox.collectPointConfirmed()) {
                    return true;
                }

                var shippingMethodComponent = registry.get('checkout.shippingMethod'),
                    provider = registry.get('checkoutProvider');

                shippingMethodComponent.validate();

                return !provider.get('params.invalid');
            }
        };

        return function (target) {
            // ensure hubbox is on and order is collectable.
            if (window.checkoutConfig.hubBox && window.checkoutConfig.hubBox.isClickAndCollectable) {
                return $.extend( target, mixin );
               // return target.extend(mixin); // new result that all other modules receive
            } else {
                return target;
            }
        }
    });
