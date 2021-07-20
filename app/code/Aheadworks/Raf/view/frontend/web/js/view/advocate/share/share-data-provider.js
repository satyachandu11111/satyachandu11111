define([
    'uiElement',
    'Aheadworks_Raf/js/model/advocate/share-data-processor',
    'Magento_Customer/js/customer-data'
], function(Element, shareDataProcessor, customerData) {
    'use strict';

    return Element.extend({
        defaults: {
            imports: {
                messageConfig: '${ $.provider }:data.messageConfig'
            },
            listens: {
                customerInfo: 'checkIfDataAvailable'
            }
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this.customerInfo = customerData.get('customer');
            this._super();
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super();
            this.observe({
                'isDataAvailable': false
            });
            return this;
        },

        /**
         * Prepare Share Data
         *
         * @returns {Object}
         */
        getData: function() {
            var customerData = this.customerInfo(),
                messageConfig = this.messageConfig;

            return shareDataProcessor.prepareData(customerData, messageConfig);
        },

        /**
         * Check if data available
         */
        checkIfDataAvailable: function () {
            if (this.customerInfo().awRafCanUseRafProgram) {
                this.isDataAvailable(true);
            }
        }
    });
});
