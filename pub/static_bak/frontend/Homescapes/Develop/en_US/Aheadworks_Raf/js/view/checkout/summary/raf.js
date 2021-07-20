define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals'
], function (Component, totals) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Raf/checkout/summary/raf'
        },

        /**
         * Order totals
         *
         * @return {Object}
         */
        totals: totals.totals(),

        /**
         * Is display raf totals
         *
         * @return {boolean}
         */
        isDisplayed: function() {
            return this.isFullMode() && this.getPureValue() != 0;
        },

        /**
         * Get title
         *
         * @return {string}
         */
        getTitle: function() {
            var raf;

            if (this.totals) {
                raf = totals.getSegment('aw_raf');
                if (raf) {
                    return raf.title;
                }
                return null;
            }
        },

        /**
         * Get total value
         *
         * @return {number}
         */
        getPureValue: function() {
            var price = 0;

            if (this.totals) {
                var raf = totals.getSegment('aw_raf');

                if (raf) {
                    price = raf.value;
                }
            }
            return price;
        },

        /**
         * Get total value
         *
         * @return {string}
         */
        getValue: function() {
            return this.getFormattedPrice(this.getPureValue());
        }
    });
});
