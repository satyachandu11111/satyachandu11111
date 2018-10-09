/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'priceUtils',
    'underscore',
    'priceBox',
    'jquery/ui'
], function ($, utils, _) {
    'use strict';

    $.widget('mageworx.optionInventory', {
        options: {
            optionConfig: {}
        },

        firstRun: function firstRun(optionConfig, productConfig, base, self)
        {
            var form = base.element,
                options = $('.product-custom-option', form);

            self._applyOptionNodeFix(options, base);
        },

        update: function update(option, optionConfig, productConfig, base)
        {
            return;
        },

        _applyOptionNodeFix: function applyOptionNodeFix(options, base)
        {
            var self = this,
                config = base.options,
                optionConfig = config.optionConfig;

            $.ajax({
                    url: self.options.stock_message_url,
                    data: {'opConfig':JSON.stringify(optionConfig)},
                    type: 'post',
                    dataType: 'json'
                })
                .done(function (response) {
                    if (response) {
                        self._updateOptionsData(options, response.result);
                    } else {
                        self._updateOptionsData(options, optionConfig);
                    }
                }).fail(
                function (response) {
                    self._updateOptionsData(options, optionConfig);
                }
            );
        },

        _updateOptionsData: function(options, opConfig)
        {
            this._updateSelectOptions(options.filter('select'), opConfig);
            this._updateInputOptions(options.filter('input'), opConfig);
        },

        _updateSelectOptions: function(options, opConfig)
        {
            options.each(function (index, element) {
                var $element = $(element);

                if ($element.hasClass('datetime-picker') ||
                    $element.hasClass('text-input') ||
                    $element.hasClass('input-text') ||
                    $element.attr('type') == 'file'
                ) {
                    return true;
                }

                var optionId = utils.findOptionId($element),
                    optionConfig = opConfig[optionId];

                $element.find('option').each(function (idx, option) {
                    var $option,
                        optionValue,
                        stockMessage;

                    $option = $(option);
                    optionValue = $option.val();

                    if (!optionValue && optionValue !== 0) {
                        return;
                    }

                    stockMessage = optionConfig[optionValue] && optionConfig[optionValue].stockMessage;

                    if (!_.isEmpty(optionConfig[optionValue].stockMessage)) {
                        $option.text($option.text() + ' ' + stockMessage);
                    }
                });
            });
        },

        _updateInputOptions: function(options, opConfig)
        {
            options.each(function (index, element) {
                var $element = $(element);

                if ($element.hasClass('datetime-picker') ||
                    $element.hasClass('text-input') ||
                    $element.hasClass('input-text') ||
                    $element.attr('type') == 'file'
                ) {
                    return true;
                }

                if ($element.attr('type') == 'checkbox' ||
                    $element.attr('type') == 'radio'
                ) {
                    return true;
                }

                var optionId = utils.findOptionId($element),
                    optionValue = $element.val();

                var optionConfig = opConfig[optionId],
                    valueText = optionConfig[optionValue] && optionConfig[optionValue].name,
                    stockMessage = optionConfig[optionValue] && optionConfig[optionValue].stockMessage,
                    valuePrice = utils.formatPrice(optionConfig[optionValue].prices.finalPrice.amount);

                if (!_.isEmpty(stockMessage)) {
                    if (optionConfig[optionValue].prices.finalPrice.amount == 0) {
                        var combinedText = valueText + ' ' + stockMessage;
                    } else {
                        var combinedText = valueText + ' + ' + valuePrice + ' ' + stockMessage;
                    }
                    $element.next('label').text(combinedText);
                }
            });
        },
    });

    return $.mageworx.optionInventory;

});