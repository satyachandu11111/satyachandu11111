define([
    'underscore',
    'uiCollection'
], function (_, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aheadworks_Raf/ui/grid/advocate/top-totals',
            imports: {
                totals: '${ $.provider }:data.topTotals'
            }
        },

        /**
         * Initializes observable properties
         *
         * @returns {TopTotals} Chainable
         */
        initObservable: function () {
            this._super()
                .track({
                    totals: []
                });

            return this;
        },

        /**
         * Check if at least one column is visible
         *
         * @return {Boolean}
         */
        atLeastOneColumnIsVisible: function () {
            return this.elems().length > 0;
        }
    });
});
