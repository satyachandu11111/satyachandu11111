define([
    'Aheadworks_Raf/js/view/checkout/summary/raf'
], function (Component) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Raf/checkout/cart/totals/raf'
        },

        /**
         * {@inheritdoc}
         */
        isDisplayed: function () {
            return this.getPureValue() != 0;
        }
    });
});
