/**
 * Copyright © Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */
define([
    'jquery',
    'Amazon_Pay/js/action/checkout-session-config-load',
    'Amazon_Pay/js/model/storage',
    'mage/url',
    'Amazon_Pay/js/amazon-checkout',
    'Magento_Customer/js/customer-data'
], function ($, checkoutSessionConfigLoad, amazonStorage, url, amazonCheckout, customerData) {
    'use strict';

    var cart = customerData.get('cart'),
        customer = customerData.get('customer'),
        canCheckoutWithAmazon = false;

    // to use Amazon Pay: customer needs to be logged in, or guest checkout allowed, or Amazon Sign-in enabled
    if (customer().firstname || amazonStorage.isGuestCheckoutEnabled || amazonStorage.isLwaEnabled) {
        canCheckoutWithAmazon = true;
    }

    if (amazonStorage.isEnabled && canCheckoutWithAmazon) {
        $.widget('amazon.AmazonButton', {
            options: {
                payOnly: null,
                placement: 'Cart',
                hideIfUnavailable: ''
            },

            _loadButtonConfig: function (callback) {
                checkoutSessionConfigLoad(function (checkoutSessionConfig) {
                    if (!$.isEmptyObject(checkoutSessionConfig)) {
                        callback({
                            merchantId: checkoutSessionConfig['merchant_id'],
                            ledgerCurrency: checkoutSessionConfig['currency'],
                            sandbox: checkoutSessionConfig['sandbox'],
                            checkoutLanguage: checkoutSessionConfig['language'],
                            productType: this._isPayOnly(checkoutSessionConfig['pay_only']) ? 'PayOnly' : 'PayAndShip',
                            placement: this.options.placement,
                            buttonColor: checkoutSessionConfig['button_color'],
                            createCheckoutSessionConfig: {
                                payloadJSON: checkoutSessionConfig['checkout_payload'],
                                signature: checkoutSessionConfig['checkout_signature'],
                                publicKeyId: checkoutSessionConfig['public_key_id'],
                            }
                        });

                        if (this.options.placement !== "Checkout") {
                            $(this.options.hideIfUnavailable).show();
                        }
                    } else {
                        $(this.options.hideIfUnavailable).hide();
                    }
                }.bind(this));
            },

            /**
             * @param {boolean} isCheckoutSessionPayOnly
             * @returns {boolean}
             * @private
             */
            _isPayOnly: function (isCheckoutSessionPayOnly) {
                var result = isCheckoutSessionPayOnly;
                if (result && this.options.payOnly !== null) {
                    result = this.options.payOnly;
                }
                return result;
            },

            /**
             * Create button
             */
            _create: function () {
                this._draw();

                if (this.options.placement == 'Product') {
                    this._redraw();
                }
            },

            /**
            * Draw button
            **/
            _draw: function () {
                var $buttonContainer = this.element;
                amazonCheckout.withAmazonCheckout(function (amazon, args) {
                    var $buttonRoot = $('<div></div>');
                    $buttonRoot.html('<img src="' + require.toUrl('images/loader-1.gif') + '" alt="" width="24" />');
                    $buttonContainer.empty().append($buttonRoot);
                    this._loadButtonConfig(function (buttonConfig) {
                        amazon.Pay.renderButton('#' + $buttonRoot.empty().removeUniqueId().uniqueId().attr('id'), buttonConfig);
                        $('.amazon-button-container .field-tooltip').fadeIn();
                        $('.amazon-checkout-button').click(function() { customerData.invalidate('*'); });
                    });
                }, this);
            },

            /**
            * Redraw button if needed
            **/
            _redraw: function () {
                var self = this;

                amazonCheckout.withAmazonCheckout(function (amazon, args) {
                    var cartData = customerData.get('cart');
                    cartData.subscribe(function (updatedCart) {
                        if (!$(self.options.hideIfUnavailable).first().is(':visible')) {
                            self._draw();
                        }
                    });
                });

            },

            click: function () {
                this.element.children().first().trigger('click');
            }
        });

        return $.amazon.AmazonButton;
    }
});
