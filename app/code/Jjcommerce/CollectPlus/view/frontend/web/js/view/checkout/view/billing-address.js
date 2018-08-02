/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * changes for billing address not to get prepopulated with CollectPlus shipping address
 */
/*jshint browser:true*/
/*global define*/
define(
    [
        'ko',
        'underscore',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-billing-address',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Ui/js/model/messageList',
        'mage/translate'
    ],
    function (
        ko,
        _,
        Component,
        customer,
        addressList,
        quote,
        createBillingAddress,
        selectBillingAddress,
        checkoutData,
        checkoutDataResolver,
        customerData,
        setBillingAddressAction,
        globalMessageList,
        $t
    ) {
        'use strict';

        var lastSelectedBillingAddress = null,
            newAddressOption = {
                getAddressInline: function () {
                return $t('New Address');
            },
                customerAddressId: null
            },
            countryData = customerData.get('directory-data'),
            addressOptions = addressList().filter(function (address) {
                return address.getType() == 'customer-address';
            });

        addressOptions.push(newAddressOption);

        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/billing-address'
            },
            currentBillingAddress: quote.billingAddress,
            addressOptions: addressOptions,
            customerHasAddresses: addressOptions.length > 1,
            collectBillingFlag: false,

            /**
             * Init component
             */
            initialize: function () {
                this._super();
                quote.paymentMethod.subscribe(function () {
                    checkoutDataResolver.resolveBillingAddress();
                }, this);
            },

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                this._super()
                    .observe({
                        selectedAddress: null,
                        isAddressDetailsVisible: quote.billingAddress() != null,
                        isAddressFormVisible: !customer.isLoggedIn() || addressOptions.length == 1,
                        isAddressSameAsShipping: false,
                        saveInAddressBook: 1
                    });

                quote.billingAddress.subscribe(function (newAddress) {
                    if (quote.isVirtual()) {
                        this.isAddressSameAsShipping(false);
                        //console.log(88);
                    } else if(quote.shippingMethod().carrier_code == 'collect') {
                        /*BOC: 2j*/
                        this.isAddressSameAsShipping(false);
                        //console.log(90);
                        //console.log(quote.shippingMethod().method_code);
                        //console.log(quote.shippingMethod().carrier_code);
                    } else {
                        this.isAddressSameAsShipping(
                            newAddress != null &&
                            newAddress.getCacheKey() == quote.shippingAddress().getCacheKey()
                        );
                        //console.log(98);
                    }
                    if(quote.shippingMethod().carrier_code != 'collect') {
                        if (newAddress != null && newAddress.saveInAddressBook !== undefined) {
                            this.saveInAddressBook(newAddress.saveInAddressBook);
                        } else {
                            this.saveInAddressBook(1);
                        }
                        this.isAddressDetailsVisible(true);
                    } else {
                        /*BOC: 2j*/
                        this.saveInAddressBook(0);
                        this.isAddressDetailsVisible(this.collectBillingFlag);
                    }
                }, this);

                return this;
            },

            canUseShippingAddress: ko.computed(function () {
                /*BOC: 2j*/
                return !quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().canUseForBilling() && (quote.shippingMethod() && quote.shippingMethod().carrier_code == 'collect' ? false : true);
            }),

            canShowBillingAddressText: ko.computed(function () {
                return (quote.shippingMethod() && quote.shippingMethod().carrier_code == 'collect' ? true : false);
            }),

            /**
             * @param {Object} address
             * @return {*}
             */
            addressOptionsText: function (address) {
                return address.getAddressInline();
            },

            /**
             * @return {Boolean}
             */
            useShippingAddress: function () {
                /*BOC: 2j*/
                if(quote.shippingMethod().carrier_code == 'collect') {
                    console.log(143);
                    lastSelectedBillingAddress = quote.billingAddress();
                    quote.billingAddress(null);
                    this.isAddressDetailsVisible(true);
                    checkoutData.setSelectedBillingAddress(null);
                    return false;
                }
                /*EOC: 2j*/
                if (this.isAddressSameAsShipping()) {
                    console.log(124);
                    selectBillingAddress(quote.shippingAddress());
                    if (window.checkoutConfig.reloadOnBillingAddress) {
                        setBillingAddressAction(globalMessageList);
                    }
                    this.isAddressDetailsVisible(true);
                } else {
                    console.log(130);
                    lastSelectedBillingAddress = quote.billingAddress();
                    quote.billingAddress(null);
                    this.isAddressDetailsVisible(false);
                }
                checkoutData.setSelectedBillingAddress(null);

                return true;
            },

            /**
             * Update address action
             */
            updateAddress: function () {
                if (this.selectedAddress() && this.selectedAddress() != newAddressOption) {
                    selectBillingAddress(this.selectedAddress());
                    checkoutData.setSelectedBillingAddress(this.selectedAddress().getKey());
                    if (window.checkoutConfig.reloadOnBillingAddress) {
                        setBillingAddressAction(globalMessageList);
                    }
                } else {
                    this.source.set('params.invalid', false);
                    this.source.trigger(this.dataScopePrefix + '.data.validate');
                    if (this.source.get(this.dataScopePrefix + '.custom_attributes')) {
                        this.source.trigger(this.dataScopePrefix + '.custom_attributes.data.validate');
                    };

                    if (!this.source.get('params.invalid')) {
                        var addressData = this.source.get(this.dataScopePrefix),
                            newBillingAddress;

                        if (customer.isLoggedIn() && !this.customerHasAddresses) {
                            this.saveInAddressBook(1);
                        }
                        addressData.save_in_address_book = this.saveInAddressBook() ? 1 : 0;
                        newBillingAddress = createBillingAddress(addressData);

                        // New address must be selected as a billing address
                        selectBillingAddress(newBillingAddress);
                        checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                        checkoutData.setNewCustomerBillingAddress(addressData);

                        /*BOC: 2j*/
                        if(quote.shippingMethod().carrier_code == 'collect') {
                            this.collectBillingFlag = true;
                            this.isAddressDetailsVisible(true);
                        } else {
                            this.collectBillingFlag = false;
                            this.isAddressDetailsVisible(false);
                        }
                        /*EOC: 2j*/
                        if (window.checkoutConfig.reloadOnBillingAddress) {
                            setBillingAddressAction(globalMessageList);
                        }
                    }
                }
            },

            /**
             * Edit address action
             */
            editAddress: function () {
                lastSelectedBillingAddress = quote.billingAddress();
                quote.billingAddress(null);
                this.isAddressDetailsVisible(false);
            },

            /**
             * Cancel address edit action
             */
            cancelAddressEdit: function () {
                this.restoreBillingAddress();
                if (quote.billingAddress()) {
                    // restore 'Same As Shipping' checkbox state
                    this.isAddressSameAsShipping(
                        quote.billingAddress() != null &&
                        quote.billingAddress().getCacheKey() == quote.shippingAddress().getCacheKey() &&
                        !quote.isVirtual()
                    );
                    this.isAddressDetailsVisible(true);
                }
                //if (quote.billingAddress() && quote.shippingMethod().carrier_code != 'collect') {
                //    // restore 'Same As Shipping' checkbox state
                //    this.isAddressSameAsShipping(
                //        quote.billingAddress() != null &&
                //            quote.billingAddress().getCacheKey() == quote.shippingAddress().getCacheKey() &&
                //            !quote.isVirtual()
                //    );console.log(201);
                //    this.isAddressDetailsVisible(true);
                //} else if(quote.billingAddress() && quote.shippingMethod().carrier_code == 'collect') {
                //    this.isAddressSameAsShipping(false);
                //    this.isAddressDetailsVisible(true);console.log(221);
                //}
            },

            /**
             * Restore billing address
             */
            restoreBillingAddress: function () {
                if (lastSelectedBillingAddress != null) {
                    selectBillingAddress(lastSelectedBillingAddress);
                }
            },

            /**
             * @param {Object} address
             */
            onAddressChange: function (address) {
                this.isAddressFormVisible(address == newAddressOption);
            },

            /**
             * @param {int} countryId
             * @return {*}
             */
            getCountryName: function (countryId) {
                return countryData()[countryId] != undefined ? countryData()[countryId].name : '';
            },
            
            /**
             * Trigger action to update shipping and billing addresses
             */
            updateAddresses: function () {
                if (window.checkoutConfig.reloadOnBillingAddress ||
                    !window.checkoutConfig.displayBillingOnPaymentMethod
                ) {
                    setBillingAddressAction(globalMessageList);
                }
            },

            /**
             * Get code
             * @param {Object} parent
             * @returns {String}
             */
            getCode: function (parent) {
                return _.isFunction(parent.getCode) ? parent.getCode() : 'shared';
            }
        });
    }
);
