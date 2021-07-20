define([
    'jquery'
], function ($) {
    'use strict';

    return {

        /**
         * Render a message replacing placeholders with a data
         *
         * @param {String} phrase
         * @param {Object} phraseArguments
         * @returns {String}
         */
         render: function (phrase, phraseArguments) {
            var resultPhrase = phrase;

            $.each(phraseArguments, function (key, value) {
                resultPhrase = resultPhrase.replace('%' + key, value);
            });

            return resultPhrase;
        }
    };
});