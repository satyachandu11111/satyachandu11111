define(
    [
        'jquery',
        'ko',
        'underscore',

        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/full-screen-loader',
        'uiRegistry',

        'HubBox_HubBox/js/model/hubbox',
        'HubBox_HubBox/js/model/checkout-data',

        'HubBox_HubBox/js/action/show-widget',
        'HubBox_HubBox/js/action/hide-widget',
        'HubBox_HubBox/js/action/unconfirm-collectpoint',
        'HubBox_HubBox/js/action/confirm-collectpoint',
        'HubBox_HubBox/js/action/unset-loading',
        'HubBox_HubBox/js/action/view-info'
    ],
    function (
        $,
        ko,
        _,

        quote,
        customer,
        checkoutData,
        addressConverter,
        fullScreenLoader,
        registry,

        hubBox,
        hbCheckoutData,

        action_showWidget,
        action_hideWidget,
        action_unConfirmCollectPoint,
        action_confirmCollectPoint,
        action_unsetLoading,
        action_viewInfo
    ) {
        'use strict';

        /**
         * return action so the user can hit return when entering a query and we'll search
         * @type {{init: ko.bindingHandlers.returnAction.init}}
         */
        ko.bindingHandlers.returnAction = {
            init: function(element, valueAccessor, allBindingsAccessor, viewModel) {
                var value = ko.utils.unwrapObservable(valueAccessor());

                $(element).keydown(function(e) {
                    if (e.which === 13) {
                        value(viewModel);
                    }
                });
            }
        };

        return {

            showClickAndCollect: ko.observable(false),

            /**
             * Launch overlay but unset collect point, let the user start again
             */
            clickAndCollect: function() {
                this.showClickAndCollect(true);
                this.unConfirmAndUnsetHubBox();
                action_showWidget();
            },

            /**
             * User clicks what is HubBox link, reset widget show first view
             */
            whatIsHubBox: function() {
                this.showClickAndCollect(true);
                this.unConfirmAndUnsetHubBox();
                action_viewInfo();
                action_showWidget();
            },

            /**
             * Get another address to select if available (logged in users)
             * @returns {*}
             */
            pickOtherCustomerAddress: function() {
                var selectAddress = null;
                if (customer.isLoggedIn()) {
                    var addresses = customer.getShippingAddressList();
                    if (addresses.length > 0) {
                        _.every(addresses, function (address) {
                            if(address.isDefaultShipping() && address.getType() !== 'hubbox-address') {
                                selectAddress = address;
                                return false;
                            }
                            return true;
                        }, this);
                    }
                }
                return selectAddress;
            },

            /**
             * Unset HubBox, select an existing shipping address if we can,
             * also clear the form via the registry, finally reset the overlay
             */
            homeDelivery: function () {
                var self = this;

                fullScreenLoader.startLoader();

                // clear cp selection
                self.unConfirmAndUnsetHubBox().then(function() {
                    // if the user is logged in and has address, just select
                    // another address to stop shipping methods getting disabled.
                    var otherAddress = self.pickOtherCustomerAddress();
                    if (otherAddress) {
                        checkoutData.setSelectedShippingAddress(otherAddress.getKey());
                        quote.shippingAddress(otherAddress);
                    } else {
                        hubBox.clearShippingFromOrder();
                    }
                    self.showClickAndCollect(false);
                    fullScreenLoader.stopLoader();
                });

                // finally reset widget
                action_viewInfo();
                action_unsetLoading();
                action_hideWidget();
            },

            /**
             * Cold boot, detect HubBox selected and setup or clear off to be safe
             */
            boot: function() {
                var self = this;
                if (hbCheckoutData.getIsHubBoxOrder()) {
                    var shippingAddress = checkoutData.getSelectedShippingAddress();
                    if (shippingAddress === 'hubbox-address') {
                        var cp = hbCheckoutData.getCollectPoint();
                        action_confirmCollectPoint(cp);
                        self.showClickAndCollect(true);
                        hubBox.setHubBox().then(function() {
                            hubBox.setHubBoxAddressToOrder(cp);
                        });
                        console.log('HubBox address detected');
                    } else {
                        self.homeDelivery();
                        console.log('HubBox set but address changed, un-setting Hubbox')
                    }
                } else {
                    self.unConfirmAndUnsetHubBox();
                }
            },

            /**
             * very dry
             * @returns {*}
             */
            unConfirmAndUnsetHubBox: function() {
                action_unConfirmCollectPoint();
                return hubBox.unsetHubBox();
            }
        };
    }
);
