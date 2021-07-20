define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Aheadworks_Raf/ui/form/advocate/label_url'
        },

        /**
         * Retrieve label for field
         *
         * @returns {String}
         */
        getLabel: function() {
            return this.source.data[this.index];
        },

        /**
         * Retrieve url for field
         *
         * @returns {String}
         */
        getUrl: function() {
            return this.source.data[this.index + '_url'];
        },
    });
});
