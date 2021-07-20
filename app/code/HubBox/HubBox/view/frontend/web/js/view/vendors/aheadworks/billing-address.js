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
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/action/create-billing-address',
        'Magento_Checkout/js/action/select-billing-address',
        'uiRegistry',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
        'HubBox_HubBox/js/model/hubbox'

    ], function (
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        checkoutData,
        createBillingAddressAction,
        selectBillingAddressAction,
        registry,
        sameAsShippingFlag,
        hubBox
    ) {
        'use strict';

        var mixin = {

            lastSelectedBillingAddress: null,

            defaults: {
                template: 'HubBox_HubBox/vendors/aheadworks/billing-address'
            },

            isHubBox: ko.computed(function(){
                return (hubBox.collectPoint() && hubBox.collectPoint().type === 'hubbox');
            }),

            canUseShippingAddress: ko.computed(function () {

                var result = !quote.isQuoteVirtual()
                    && quote.shippingAddress()
                    && quote.shippingAddress().canUseForBilling()
                    && !hubBox.collectPointConfirmed();

                return result;
            }),

            isFormInline: ko.computed(function () {
                var addressOptions = addressList().filter(function (address) {
                    return address.getType() !== 'hubbox-address';
                });

                return addressOptions.length == 0 || hubBox.collectPointConfirmed();
            }),

            /*
             * This function has been duplicated because the value for isFormInline
             * needs to be computed to take into account changes to the
             * addresses made by HubBox in the function above
             */
            onUseShippingAddress: function () {
                var addressData,
                    newBillingAddress;

                if (sameAsShippingFlag.sameAsShipping()) {
                    selectBillingAddressAction(quote.shippingAddress());
                    this.updateAddresses();
                    checkoutData.setSelectedBillingAddress(quote.shippingAddress().getKey());
                    checkoutData.setNewCustomerBillingAddress(null);
                } else {
                    this.lastSelectedBillingAddress = quote.billingAddress();
                    // This is where the isFormInline has changed
                    if (this.isFormInline()) {
                        addressData = this.source.get('billingAddress');
                        newBillingAddress = createBillingAddressAction(addressData);
                        selectBillingAddressAction(newBillingAddress);
                        checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                        checkoutData.setNewCustomerBillingAddress(addressData);
                    } else {
                        checkoutData.setSelectedBillingAddress(null);
                        checkoutData.setNewCustomerBillingAddress(null);
                    }
                }

                return true;
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                this._super();

                this.showAddressDetails = ko.computed(function () {
                    if (hubBox.collectPointConfirmed()) {
                        return false;
                    }

                    return this.isAddressSpecified();

                }, this);

                this.showAddressList = ko.computed(function () {
                    if (hubBox.collectPointConfirmed()) {
                        return false;
                    }

                    return !this.isAddressSpecified() || this.editAddress();

                }, this);

                this.showForm = ko.computed(function () {
                    if (hubBox.collectPointConfirmed()) {
                        return true;
                    }

                    if (this.isFormInline()) {
                        return !quote.isQuoteVirtual() && !sameAsShippingFlag.sameAsShipping()
                            || quote.isQuoteVirtual();
                    }

                    return this.showAddressList() && this.selectedAddress() == this.newAddressOption;

                }, this);

                this.showToolbar = ko.computed(function () {
                    if (hubBox.collectPointConfirmed()) {
                        return false;
                    }

                    return this.showAddressList() || this.showForm();

                }, this);

                return this;
            }
        };

        return function (target) {
            // ensure hubbox is on and order is collectable.
            if (window.checkoutConfig.hubBox && window.checkoutConfig.hubBox.isClickAndCollectable) {
                return target.extend(mixin); // new result that all other modules receive
            } else {
                return target;
            }
        }
    });