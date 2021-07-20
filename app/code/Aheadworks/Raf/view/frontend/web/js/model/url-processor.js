define([
    'jquery'
], function ($) {
    'use strict';

    return {

        /**
         * Remove params from url and return modified url
         *
         * @param {String} url
         * @param {Array} paramNames
         * @returns {String}
         */
        removeParams: function (url, paramNames) {
            var urlData = this._parseUrl(url);

            $.each(paramNames, function (paramName) {
                if (urlData.params[paramName]) {
                    delete urlData.params[paramName];
                }
            });

            return this._buildUrl(urlData);
        },

        /**
         * Add params to url and return modified url
         *
         * @param {String} url
         * @param {Array} paramNames
         * @returns {String}
         */
        addParams: function (url, paramNames) {
            var urlData = this._parseUrl(url);

            $.each(paramNames, function (paramName, paramValue) {
                if (!urlData.params[paramName]) {
                    urlData.params[paramName] = paramValue;
                }
            });

            return this._buildUrl(urlData);
        },

        /**
         * Parse url
         *
         * @param {String} url
         * @returns {Object}
         */
        _parseUrl: function (url) {
            var decode = window.decodeURIComponent,
                urlPaths = url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].replace(/#$/, '').split('&') : [],
                paramData = {},
                parameters;

            for (var i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[decode(parameters[0])] = parameters[1] !== undefined
                    ? decode(parameters[1].replace(/\+/g, '%20'))
                    : '';
            }

            return {baseUrl: baseUrl, params: paramData};
        },

        /**
         * Build url
         *
         * @param {String} urlData
         * @returns {String}
         */
        _buildUrl: function (urlData) {
            var params = $.param(urlData.params);

            return urlData.baseUrl + (params.length ? '?' + params : '');
        }
    }
});
