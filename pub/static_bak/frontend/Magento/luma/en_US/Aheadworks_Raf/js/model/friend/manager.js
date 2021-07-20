define([
    'jquery',
    './friend-data'
], function($, friendData) {
    'use strict';

    return {
        /**
         * Can display welcome popup
         *
         * @returns {Boolean}
         */
        canDisplayWelcomePopup: function () {
            return friendData.isDisplayWelcomePopup();
        },

        /**
         * Welcome popup was viewed
         */
        welcomePopupViewed: function () {
            friendData.setIsDisplayWelcomePopup()
        }
    };
});
