define(
    [
        'ko',
        'HubBox_HubBox/js/view/helper'
    ],function (
        ko,
        hubBoxHelper
    ) {
        'use strict';

        var mixin = {
            defaults: {
                template: 'HubBox_HubBox/vendors/amazon/checkout-revert'
            },
            showClickAndCollect: hubBoxHelper.showClickAndCollect
        };

        return function (target) {
            // ensure hubbox is on and order is collectable.
            if (window.checkoutConfig.hubBox && window.checkoutConfig.hubBox.isClickAndCollectable) {
                return target.extend(mixin); // new result that all other modules receive
            } else {
                return target;
            }
        }
    });
