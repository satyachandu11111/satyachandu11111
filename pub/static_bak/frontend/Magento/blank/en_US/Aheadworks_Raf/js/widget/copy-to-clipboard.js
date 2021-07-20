define([
    'jquery'
], function ($) {
    "use strict";

    $.widget('mage.awRafCopyToClipboard', {
        options: {
            textFieldSelector: '[data-role="referral-url-text"]'
        },

        _create: function () {
            this._bind();
        },

        /**
         * Bind event
         * @private
         */
        _bind: function () {
            this._on({
                'click': function () {
                    this._copyTextToClipboard();
                }
            });
        },

        /**
         * Copy text to clipboard
         * @private
         */
        _copyTextToClipboard: function () {
            $(this.options.textFieldSelector).select();
            document.execCommand('Copy');
        }
    });

    return $.mage.awRafCopyToClipboard;
});