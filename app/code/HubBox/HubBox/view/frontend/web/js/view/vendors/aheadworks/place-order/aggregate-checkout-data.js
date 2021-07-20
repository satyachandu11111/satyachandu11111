define(
    [
        'jquery',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/action/select-billing-address',
        'Aheadworks_OneStepCheckout/js/action/set-shipping-information',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
        'Aheadworks_OneStepCheckout/js/model/checkout-section/service-enable-flag',
        'Aheadworks_OneStepCheckout/js/model/completeness-logger/service-enable-flag',

        'HubBox_HubBox/js/model/hubbox'
    ],function (
        $,
        registry,
        quote,
        selectShippingAddressAction,
        selectBillingAddressAction,
        setShippingInformationAction,
        sameAsShippingFlag,
        sectionsServiceEnableFlag,
        completenessLoggerServiceEnableFlag,

        hubBox
    ) {
        'use strict';

        var mixin = {

            /**
             * Set shipping information
             *
             * @returns {Object}
             */
            _setShippingInformation: function () {
                var shippingAddressComponent = registry.get('checkout.shippingAddress'),
                    shippingAddress = quote.shippingAddress();

                if (!quote.isQuoteVirtual() && !hubBox.collectPointConfirmed()) {
                    if (shippingAddressComponent.useFormData()) {
                        shippingAddress = shippingAddressComponent.copyFormDataToQuoteData(shippingAddress);
                    }

                    this._disableServices();
                    selectShippingAddressAction(shippingAddress);
                    this._enableServices();

                    return setShippingInformationAction();
                }
                return $.Deferred().resolve();
            }
        };

        return function (target) {
            // ensure HubBox is on and order is collectable.
            if (window.checkoutConfig.hubBox && window.checkoutConfig.hubBox.isClickAndCollectable) {
                return $.extend( target, mixin );
            } else {
                return target;
            }
        }
    });
