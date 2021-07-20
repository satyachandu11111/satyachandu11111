define([
    'jquery',
    'mage/cookies'
], function($) {
    'use strict';

    var awRafWelcomePopupCookieKey = 'aw-raf-welcome-popup',
        awRafCookieLifeTime = 31556926;

    return {
        /**
         * Set is display welcome popup
         *
         * @param {Boolean} value
         */
        setIsDisplayWelcomePopup: function () {
            $.mage.cookies.set(awRafWelcomePopupCookieKey, 'is_show', {lifetime: awRafCookieLifeTime});
        },

        /**
         * Is display welcome popup
         *
         * @return {Boolean}
         */
        isDisplayWelcomePopup: function () {
            return $.mage.cookies.get(awRafWelcomePopupCookieKey) === 'show';
        }
    };
});
