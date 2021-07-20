define([
    'jquery',
    'Aheadworks_Raf/js/model/url',
    'Aheadworks_Raf/js/model/phrase/renderer/placeholder',
    'mage/translate'
], function($, url, phraseRenderer, $t) {
    'use strict';

    return {

        /**
         * Prepare Share Data
         *
         * @param {Object} customerData
         * @param {Object} messageConfig
         * @returns {Object}}
         */
        prepareData: function(customerData, messageConfig) {
            var shareData = {};

            if (customerData.awRafCanUseRafProgram && messageConfig.activeRuleData) {
                shareData.referralUrl = this.prepareReferralUrl(customerData);
                shareData.invitationMessage = this.prepareInvitationMessage(messageConfig);
            }
            return shareData;
        },

        /**
         * Prepare referral URL for advocate
         *
         * @param {Object} customerData
         * @returns {String}
         */
        prepareReferralUrl: function(customerData) {
            var urlParams = {},
                currentUrl = window.location.href;

            urlParams[customerData.awRafExternalLinkParam] = customerData.awRafExternalLinkValue;
            if (currentUrl.includes('aw_raf/advocate')) {
                return url.addParamsToUrl(customerData.awRafBaseUrl, urlParams);
            }

            return url.addParamsToUrl(currentUrl, urlParams);
        },

        /**
         * Prepare invitation message for advocate
         *
         * @param {Object} messageConfig
         * @returns {String}
         * @private
         */
        prepareInvitationMessage: function(messageConfig) {
            var placeholderData = {},
                message;

            placeholderData.friend_reward = messageConfig.activeRuleData.friend_off;
            placeholderData.store_name = messageConfig.storeName;

            if (messageConfig.activeRuleData.registration_required) {
                message = phraseRenderer.render(
                    $t('Register and get %friend_reward on your first purchase on %store_name!'),
                    placeholderData
                );
            } else {
                message = phraseRenderer.render(
                    $t('Click here to get %friend_reward on your first purchase on %store_name!'),
                    placeholderData
                );
            }

            return message;
        }
    };
});
