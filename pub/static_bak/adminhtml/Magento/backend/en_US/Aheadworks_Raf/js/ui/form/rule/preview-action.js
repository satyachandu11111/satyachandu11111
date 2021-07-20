define([
    'uiCollection',
    'underscore'
], function (Collection, _) {
    'use strict';

    return Collection.extend({
        defaults: {
            listens: {
                elems: 'initPreviewMode'
            }
        },

        /**
         * Init observable variable for each child of collection
         */
        initPreviewMode: function () {
            _.each(this.elems(), function (elem) {
                elem.observe({
                    'previewMode': true
                });
            });
        },

        /**
         * Toggle preview mode for element
         * @param {Object} elem
         */
        togglePreviewMode: function(elem) {
            elem.previewMode(!elem.previewMode());

            if (!elem.previewMode()) {
               elem.focused(true);
            }
        },

        /**
         * Enable preview mode for element
         * @param {Object} elem
         */
        enablePreviewMode: function (elem) {
            if (!elem.error()) {
                elem.previewMode(true);
            }
        }
    });
});
