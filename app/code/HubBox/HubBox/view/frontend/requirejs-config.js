
var config = {
    paths: {
        googlemaps: "HubBox_HubBox/js/lib/googlemaps",
        async: "HubBox_HubBox/js/lib/async"
    },
    config: {
        mixins: {

            // Default Magento Checkout
            'Magento_Checkout/js/view/shipping': {
                'HubBox_HubBox/js/view/vendors/magento/shipping': true
            },
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': {
                'HubBox_HubBox/js/view/vendors/magento/shipping-information/address-renderer/default': true
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'HubBox_HubBox/js/view/vendors/magento/shipping-address/address-renderer/default': true
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'HubBox_HubBox/js/view/vendors/magento/checkout-data-resolver': true
            },

            // Aheadworks One Step Checkout
            'Aheadworks_OneStepCheckout/js/view/shipping-address': {
                'HubBox_HubBox/js/view/vendors/aheadworks/shipping-address': true
            },
            'Aheadworks_OneStepCheckout/js/view/billing-address': {
                'HubBox_HubBox/js/view/vendors/aheadworks/billing-address': true
            },
            'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-validator': {
                'HubBox_HubBox/js/view/vendors/aheadworks/place-order/aggregate-validator': true
            },
            'Aheadworks_OneStepCheckout/js/view/shipping-address/address-renderer/default': {
                'HubBox_HubBox/js/view/vendors/aheadworks/shipping-address/address-renderer/default': true
            },
            'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-checkout-data': {
                'HubBox_HubBox/js/view/vendors/aheadworks/place-order/aggregate-checkout-data': true
            },

            // Amazon
            'Amazon_Payment/js/view/checkout-revert': {
                'HubBox_HubBox/js/view/vendors/amazon/checkout-revert': true
            },
            'Amazon_Payment/js/view/checkout-widget-address': {
                'HubBox_HubBox/js/view/vendors/amazon/checkout-widget-address': true
            }
        }

    },
    map: {
        '*': {
            'Magento_Checkout/js/model/shipping-save-processor/default':
                'HubBox_HubBox/js/map/magento/shipping-save-processor/default',
            'Magento_Checkout/js/model/new-customer-address':
                'HubBox_HubBox/js/map/magento/new-customer-address',

            // Amazon
            'Amazon_Payment/js/view/shipping':
                'HubBox_HubBox/js/map/amazon/shipping'
        }
    }
};
