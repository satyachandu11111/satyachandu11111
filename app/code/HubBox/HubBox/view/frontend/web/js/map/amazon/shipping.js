/*global define*/
define(
    [
        'jquery',
        "underscore",
        'ko',
        'Magento_Checkout/js/view/shipping',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Amazon_Payment/js/model/storage',
        'HubBox_HubBox/js/view/helper'
    ],
    function (
        $,
        _,
        ko,
        Component,
        customer,
        setShippingInformationAction,
        stepNavigator,
        amazonStorage,
        hubBoxHelper
    ) {
        'use strict';
        return Component.extend({
            initialize: function () {
                this._super();
                this.isNewAddressAdded(amazonStorage.isAmazonAccountLoggedIn());
                amazonStorage.isAmazonAccountLoggedIn.subscribe(function (value) {
                    this.isNewAddressAdded(value);
                }, this);
                return this;
            },
            validateGuestEmail: function () {
                var loginFormSelector = 'form[data-role=email-with-possible-login]';
                $(loginFormSelector).validation();
                return $(loginFormSelector + ' input[type=email]').valid();
            },
            /**
             * New setShipping Action for Amazon Pay to bypass validation
             */
            setShippingInformation: function () {
                function setShippingInformationAmazon()
                {
                    setShippingInformationAction().done(
                        function () {
                            stepNavigator.next();
                        }
                    );
                }
                if (hubBoxHelper.showClickAndCollect()) {

                    if (this.validateClickAndCollectShippingInformation()) {
                        setShippingInformationAction().done(
                            function () {
                                stepNavigator.next();
                            }
                        );
                    }

                } else {

                    if (amazonStorage.isAmazonAccountLoggedIn() && customer.isLoggedIn()) {
                        setShippingInformationAmazon();
                    } else if (amazonStorage.isAmazonAccountLoggedIn() && !customer.isLoggedIn()) {
                        if (this.validateGuestEmail()) {
                            setShippingInformationAmazon();
                        }
                    } else {
                        //if using guest checkout or guest checkout with amazon pay we need to use the main validation
                        if (this.validateShippingInformation()) {
                            setShippingInformationAmazon();
                        }
                    }
                }
            }
        });
    }
);
