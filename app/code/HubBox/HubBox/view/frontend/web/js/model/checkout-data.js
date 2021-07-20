define(
    [
        'jquery',
        'Magento_Customer/js/customer-data'
    ],
    function ($, customerData) {
        'use strict';

        var cacheKey = 'hubbox-checkout-data';

        /**
         * Retrieve checkout data from storage
         *
         * @returns {Object}
         */
        var getData = function () {
            return customerData.get(cacheKey)();
        };

        /**
         * Save checkout data to storage
         *
         * @param {Object} data
         */
        var saveData = function (data) {
            customerData.set(cacheKey, data);
        };

        if ($.isEmptyObject(getData())) {
            var hbCheckoutData = {
                'collectPoint': null
            };
            saveData(hbCheckoutData);
        }

        return {

            /**
             * Get is HubBox collect point selected order
             * @returns {boolean}
             */
            getIsHubBoxOrder: function () {
                return getData().collectPoint !== undefined && getData().collectPoint !== null;
            },

            /**
             * Get collect point
             *
             * @returns {string}
             */
            getCollectPoint: function () {
                return getData().collectPoint;
            },

            /**
             * Set collect point
             *
             * @param {string} Id
             */
            setCollectPoint: function (cp) {
                var obj = getData();

                obj.collectPoint = cp;
                saveData(obj);
            }
        }
    }
);
