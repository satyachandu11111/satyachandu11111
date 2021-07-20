define(
    [
        'ko',
        'HubBox_HubBox/js/view/helper',
        'HubBox_HubBox/js/model/checkout-data'
    ],function (
        ko,
        hubBoxHelper,
        hbCheckoutData
    ) {
        'use strict';

        var mixin = {
            defaults: {
                template: 'HubBox_HubBox/vendors/amazon/checkout-widget-address'
            },
            showClickAndCollect: hubBoxHelper.showClickAndCollect,

            getShippingAddressFromAmazon : function() {
                if (!hbCheckoutData.getIsHubBoxOrder()) {
                    this._super();
                }
            }
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

