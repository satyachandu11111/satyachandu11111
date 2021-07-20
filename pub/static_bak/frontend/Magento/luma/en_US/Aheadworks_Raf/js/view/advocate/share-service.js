define([
    'uiCollection',
    'underscore'
], function (Collection, _) {
    'use strict';

    return Collection.extend({
        defaults: {
            imports: {
                isShareDataAvailable: '${ $.provider }:isDataAvailable'
            },
            listens: {
                isShareDataAvailable: 'updateChildData',
                elems: 'updateChildData'
            },
            modules: {
                shareDataProvider: '${ $.provider }'
            }
        },

        /**
         * Update child UI component data
         */
        updateChildData: function() {
            var advocateShareData = this.shareDataProvider().getData();

            if (!_.isEmpty(advocateShareData)) {
                _.each(this.elems(), function (elem) {
                    elem.updateData(advocateShareData);
                });
            }
        }
    });
});