define([
    'jquery',
    'Aheadworks_Raf/js/model/url-processor'
], function ($, processor) {
    'use strict';

    return {
        currentUrl: window.location.href,

        /**
         * Remove params from url and return a modified one
         *
         * @param {String} url
         * @param {Array} params
         * @returns {String}
         */
        removeParamsFromUrl: function (url, params) {
            return processor.removeParams(url, params);
        },

        /**
         * Add params to url and return a modified one
         *
         * @param {String} url
         * @param {Array} params
         * @returns {String}
         */
        addParamsToUrl: function (url, params) {
            return processor.addParams(url, params);
        }
    };
});