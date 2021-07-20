/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @api
 */
define([
    'underscore',
    'Magento_Checkout/js/model/default-post-code-resolver',
    'HubBox_HubBox/js/model/checkout-data'
], function (
    _,
    DefaultPostCodeResolver,
    hbCheckoutData
) {
    'use strict';

    /**
     * @param {Object} addressData
     * Returns new address object
     */
    return function (addressData) {
        var identifier = Date.now(),
            regionId;

        if (addressData.region && addressData.region['region_id']) {
            regionId = addressData.region['region_id'];
        } else if (addressData['country_id'] && addressData['country_id'] == window.checkoutConfig.defaultCountryId) { //eslint-disable-line
            regionId = window.checkoutConfig.defaultRegionId || undefined;
        }

        var saveAddress = 0;
        if (!hbCheckoutData.getIsHubBoxOrder()) {
            saveAddress = addressData['save_in_address_book'];
        }

        return {

            isHubBoxAddress : function() {
                var returnValue = false;

                if (addressData['custom_attributes']) {
                    if (Array.isArray(addressData['custom_attributes'])) {
                        addressData['custom_attributes'].map((el) => {
                            if (el.attribute_code == 'isHubBoxAddress' && el.value == true) {
                                returnValue = true;
                            }
                        });
                    } else if (addressData['custom_attributes'].isHubBoxAddress) {
                        returnValue = true;
                    }
                }

                return returnValue;
            },

            email: addressData.email,
            countryId: addressData['country_id'] || addressData.countryId || window.checkoutConfig.defaultCountryId,
            regionId: regionId || addressData.regionId,
            regionCode: addressData.region ? addressData.region['region_code'] : null,
            region: addressData.region ? addressData.region.region : null,
            customerId: addressData['customer_id'] || addressData.customerId,
            street: addressData.street ? _.compact(addressData.street) : addressData.street,
            company: addressData.company,
            telephone: addressData.telephone,
            fax: addressData.fax,
            postcode: addressData.postcode ? addressData.postcode : DefaultPostCodeResolver.resolve(),
            city: addressData.city,
            firstname: addressData.firstname,
            lastname: addressData.lastname,
            middlename: addressData.middlename,
            prefix: addressData.prefix,
            suffix: addressData.suffix,
            vatId: addressData['vat_id'],
            save_in_address_book: saveAddress,

            customAttributes: addressData['custom_attributes'],

            /**
             * @return {*}
             */
            isDefaultShipping: function () {
                return addressData['default_shipping'];
            },

            /**
             * @return {*}
             */
            isDefaultBilling: function () {
                return addressData['default_billing'];
            },

            /**
             * @return {String}
             */
            getType: function () {
                return (this.isHubBoxAddress()) ? 'hubbox-address' : 'new-customer-address';
            },

            /**
             * @return {String}
             */
            getKey: function () {
                return this.getType();
            },

            /**
             * @return {String}
             */
            getCacheKey: function () {
                return this.getType() + identifier;
            },

            /**
             * @return {Boolean}
             */
            isEditable: function () {
                return (!this.isHubBoxAddress());
            },

            /**
             * @return {Boolean}
             */
            // cannot use hb address as billing
            canUseForBilling : function () {
                return (!this.isHubBoxAddress());
            }
        };
    };
});
