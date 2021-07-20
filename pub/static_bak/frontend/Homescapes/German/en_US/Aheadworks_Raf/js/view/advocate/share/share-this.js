define([
    'Aheadworks_Raf/js/view/advocate/share/abstract-service',
    'jquery'
], function (AbstractService, $) {
    'use strict';

    return AbstractService.extend({
        defaults: {
            st_sticky_selector: '.sharethis-sticky-share-buttons',
            st_inline_selector: '.sharethis-inline-share-buttons'
        },

        /**
         * Prepare 'share this' data
         */
        prepareServiceData: function () {
            if (this.isAllowed()) {
                this._updateShareThisElement(this.st_sticky_selector);
                this._updateShareThisElement(this.st_inline_selector);
            }
        },

        /**
         * Update share this element on the page
         *
         * @param {String} shareThisElement
         * @private
         */
        _updateShareThisElement: function (shareThisElement) {
            $(shareThisElement).attr({
                'data-url': this.advocateShareData.referralUrl,
                'data-title': this.advocateShareData.invitationMessage
            });
        }
    });
});