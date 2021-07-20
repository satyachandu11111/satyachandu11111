define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mageUtils'
], function($, modal, utils) {
    'use strict';

    $.widget('mage.awRafPopup', {
        options: {
            autoRender: true,
            popupContentSelector: '[data-role="aw-raf-friend-popup-content"]',
            popupClass: 'aw-raf__friend-popup'
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this._bind();

            if (this.options.autoRender) {
                this.displayPopup();
            }
        },

        /**
         * Display popup
         */
        displayPopup: function () {
            this._createPopup();
            $(this.options.popupContentSelector).modal('openModal');
        },

        /**
         * Bind event
         * @private
         */
        _bind: function () {
            $(document).on('click', $.proxy(this._onClickDocument, this));
        },

        /**
         * Create popup
         * @private
         */
        _createPopup: function () {
            var options = {
                'type': 'popup',
                'modalClass': this.options.popupClass,
                'responsive': true,
                'innerScroll': true,
                'buttons': []
            };

            modal(options, $(this.options.popupContentSelector));
        },

        /**
         * Click on document
         * @private
         */
        _onClickDocument: function(e) {
            var popupContent = $(this.options.popupContentSelector),
                popupData = popupContent.data('mageModal');

            if (!utils.isEmpty(popupData) && popupData.options.isOpen
                && !popupContent.is(e.target) && popupContent.has(e.target).length === 0
            ) {
                popupContent.modal('closeModal');
            }
        }
    });

    return $.mage.awRafPopup;
});
