define([
    'mage/utils/wrapper',

    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/address-converter',


    'HubBox_HubBox/js/model/hubbox',
    'HubBox_HubBox/js/model/checkout-data',

], function (
    wrapper,

    checkoutData,
    selectShippingAddress,
    addressConverter,

    hubBox,
    hbCheckoutData
) {
    'use strict';

    return function (target) {

        var applyShippingAddressWrapped = wrapper.wrap(target.applyShippingAddress, function (_super, items) {

            if (hbCheckoutData.getIsHubBoxOrder()) {

                // check shipping address looks like a hubBox address
                // check to see if there previously was a hubBox location selected and we now have a refresh
                var shippingAddress = checkoutData.getShippingAddressFromData();

                if (shippingAddress) {

                    if (shippingAddress.company && shippingAddress.company.indexOf("HubBox") !== -1) {

                        var cp = hbCheckoutData.getCollectPoint();
                        hubBox.setHubBoxAddressToOrder(cp);

                        return true;
                    }
                }
            }

            var result = _super(items); // call original method
            return result;
        });

        target.applyShippingAddress = applyShippingAddressWrapped; // replace original method with wrapped version

        return target;
    }
});




