define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service',

        'HubBox_HubBox/js/model/hubbox',
        'HubBox_HubBox/js/model/checkout-data',
        'HubBox_HubBox/js/view/helper'
    ],function (
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        $t,
        srs,

        hubBox,
        hbCheckoutData,
        hubBoxHelper
    ) {
        'use strict';

        var mixin = {

            defaults: {
                template: 'HubBox_HubBox/vendors/magento/shipping'
            },

            visible: ko.observable(!quote.isVirtual()),

            setShippingInformation: function () {
                if (hubBoxHelper.showClickAndCollect()) {
                    if (this.validateClickAndCollectShippingInformation()) {
                        setShippingInformationAction().done(
                            function () {
                                stepNavigator.next();
                            }
                        );
                    }

                } else {

                    if (this.validateShippingInformation()) {
                        setShippingInformationAction().done(
                            function () {
                                stepNavigator.next();
                            }
                        );
                    }
                }
            },

            validateClickAndCollectShippingInformation: function() {

                var loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn(),
                    hbcheckoutform = '#hb-checkout-form', hbcheckoutformResult = false;

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                } else {
                    if (!checkoutData.getSelectedShippingAddress()) {
                        this.errorValidationMessage('Please select a shipping address');
                        return false;
                    }
                }

                if (!emailValidationResult) {
                    this.errorValidationMessage('Please enter a valid email address');
                    return false;
                }

                if(!hubBox.collectPointConfirmed()) {
                    this.errorValidationMessage('Please choose a hub box location.');
                    return false;
                } else {
                    if (window.checkoutConfig.hubBox.showFirstLastname) {
                        $(hbcheckoutform).validation();
                        hbcheckoutformResult = Boolean($(hbcheckoutform + ' input[name^="hubboxAdditional"]').valid());

                        if (!hbcheckoutformResult) {
                            this.errorValidationMessage('Missing required information');
                            return false;
                        }

                        hubBox.updateFirstLastname(
                            $(hbcheckoutform + ' input[name="hubboxAdditional[firstname]"]').val(),
                            $(hbcheckoutform + ' input[name="hubboxAdditional[lastname]"]').val()
                        );
                    }
                }

                return true;
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
