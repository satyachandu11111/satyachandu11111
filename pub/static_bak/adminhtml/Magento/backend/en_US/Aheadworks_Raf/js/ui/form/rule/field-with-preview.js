define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            listens: {
                error: 'checkError'
            }
        },

        /**
         * Prepare preview for field
         *
         * @returns {String}
         */
        getPreview: function() {
            if (!this.value()) {
                return '...';
            }
            return this._super();
        },

        /**
         * Check validation errors
         */
        checkError: function () {
            if (!this.error()) {
                this.previewMode(true);
            } else {
                this.previewMode(false);
            }
        }
    });
});
