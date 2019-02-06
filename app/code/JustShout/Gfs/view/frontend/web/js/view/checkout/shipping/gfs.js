define([
    'jquery',
    "underscore",
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'mage/template',
    'mage/storage'
], function (
    $,
    _,
    ko,
    Component,
    quote,
    shippingService,
    mageTemplate,
    storage
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'JustShout_Gfs/view/checkout/shipping/gfs',
            shippingMethod: '',
            checkoutWidgetContainer: '#gfs-checkout-widget-container',
            widgetInstance: '#gfs-checkout-widget-container gfs-checkout',
            checkoutWidgetTemplate: '#gfs-checkout-widget-template'
        },
        initialize: function ()
        {
            var self = this;
            this._super();

            quote.shippingMethod.subscribe(function (method) {
                self.removeGfsCheckoutComponent();
                self.addGfsCheckoutComponent();
            });

            quote.shippingAddress.subscribe(function (address) {
                if (!window.gfsData.currentPostcode) {
                    window.gfsData.currentPostcode = address.postcode;
                }

                if (window.gfsData.currentPostcode !== address.postcode) {
                    self.removeGfsCheckoutComponent();
                    self.addGfsCheckoutComponent();
                    window.gfsData.currentPostcode = address.postcode;
                }
            });

            return this;
        },
        /**
         * This method will generate the Gfs Checkout Widget
         *
         * @return void
         */
        addGfsCheckoutComponent: function ()
        {
            var self = this;
            this.removeGfsCheckoutComponent();
            this.triggerProcessStart();
            storage.get('gfs/data/generate').done(function (response) {
                if (response.data) {
                    $(self.checkoutWidgetContainer).html(self.generateGfsWidgetHtml(response));
                    $('.gfs-shipping-information-content').html(self.generateGfsAddressWidgetHtml(response));
                    self.bindGfsEvents();
                }
                self.triggerProcessStop();
            }).fail(function() {
                self.triggerProcessStop();
            });
        },
        /**
         * This method will remove the Gfs Checkout Widget
         *
         * @return void
         */
        removeGfsCheckoutComponent : function()
        {
            $(this.widgetInstance)
                .off('getStandardSelectedService')
                .off('getCalendarSelectedService')
                .off('_droppointChanged');

            $(this.checkoutWidgetContainer).html('');
        },
        /**
         * This method will generate the html used for the gfs widget
         *
         * @param {Object} response
         *
         * @return string
         */
        generateGfsWidgetHtml: function(response)
        {
            var gfsData = btoa(JSON.stringify(response.data)),
                initialAddress = response.initial_address,
                gfsWidgetTemplate = mageTemplate('#gfs-checkout-widget-template');

            return gfsWidgetTemplate({
                data: {
                    'access_token': window.gfsData.accessToken,
                    'currency_symbol': window.gfsData.currency_symbol,
                    'delivery_types': window.gfsData.delivery_types,
                    'standard_delivery_title': window.gfsData.standard_delivery_title,
                    'calendar_delivery_title': window.gfsData.calendar_delivery_title,
                    'drop_point_title': window.gfsData.drop_point_title,
                    'service_sort_order': window.gfsData.service_sort_order,
                    'home_icon': window.gfsData.home_icon,
                    'use_standard': window.gfsData.use_standard,
                    'use_calendar': window.gfsData.use_calendar,
                    'use_drop_points': window.gfsData.use_drop_points,
                    'default_service': window.gfsData.default_service,
                    'default_carrier': window.gfsData.default_carrier,
                    'default_carrier_code': window.gfsData.default_carrier_code,
                    'default_price': window.gfsData.default_price,
                    'default_min_delivery_time': window.gfsData.default_min_delivery_time,
                    'default_max_delivery_time': window.gfsData.default_max_delivery_time,
                    'gfs_data': gfsData,
                    'initial_address': initialAddress
                }
            });
        },
        /**
         * This method will generate the delivery address widget
         *
         * @param {Object} response
         *
         * @return string
         */
        generateGfsAddressWidgetHtml: function(response)
        {
            var initialAddress = response.initial_address,
                gfsWidgetTemplate = mageTemplate('#gfs-delivery-address-template');

            return gfsWidgetTemplate({
                data: {
                    'address': initialAddress
                }
            });
        },
        /**
         * Bind events from gfs checkout widget to callbacks
         *
         * @return void
         */
        bindGfsEvents : function()
        {
            var self = this;
            $(this.widgetInstance).on('getStandardSelectedService', function(data) {
                self.setGfsShippingData(data, 'standard');
            }).on('getCalendarSelectedService', function(data) {
                self.setGfsShippingData(data, 'calendar');
            }).on('getDroppointSelectedService', function(data) {
                self.setGfsShippingData(data, 'droppoint');
            });
        },
        /**
         * Set Shipping Data when method is selected
         *
         * @param data
         * @param shippingMethodType
         *
         * @return void
         */
        setGfsShippingData: function(data, shippingMethodType)
        {
            if (jQuery.isEmptyObject(data.detail)) {
                return;
            }
            var result = Object.create(data.detail);
            for(var key in result) {
                result[key] = result[key];
            }
            result.shippingMethodType = shippingMethodType;
            window.gfsShippingData = JSON.stringify(result);
        },
        /**
         * This function will start the loading animation
         *
         * @return void
         */
        triggerProcessStart : function()
        {
            $('body').trigger('processStart');
        },
        /**
         * This function will stop the loading animation
         *
         * @return void
         */
        triggerProcessStop : function()
        {
            $('body').trigger('processStop');
        }
    });
});
