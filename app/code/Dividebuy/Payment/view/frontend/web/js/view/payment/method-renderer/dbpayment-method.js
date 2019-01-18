/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'mage/url'
    ],
    function ($, Component, placeOrderAction, url) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Dividebuy_Payment/payment/dbpayment'
            },
             /** Returns payment acceptance mark image path */
            getDivideBuySrc: function () {
                return window.checkoutConfig.payment.dividebuy.imageSrc;
            },
             placeOrder: function (data, event) {
                if (event) {
                    event.preventDefault();
                }
                var self = this,
                    placeOrder;
                
               // if (emailValidationResult && this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

                    $.when(placeOrder).fail(function () {
                        self.isPlaceOrderActionAllowed(true);
                    }).done(this.afterPlaceOrder.bind(this));
                    return true;
                //}
                //return false;
            },
            afterPlaceOrder: function () {
                window.location.replace(url.build('checkoutconfig/index/test'));
            },         
        });
    }
);
