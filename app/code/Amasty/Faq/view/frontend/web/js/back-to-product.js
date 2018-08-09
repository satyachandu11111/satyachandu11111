/*global window*/
define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/storage'
], function ($, Component, customerData) {
    return Component.extend({
        defaults: {
            getDataUrl: '',
            template: 'Amasty_Faq/backto'
        },
        initialize: function (config) {
            this.faqProd = customerData.get('faq_product');

            this._super();

            if (!this.faqProd().url) {
                //fix for magento2.2
                var faqProductStorage = $.initNamespaceStorage('mage-cache-storage').localStorage.get('faq_product');
                if (faqProductStorage) {
                    customerData.set('faq_product', faqProductStorage);
                }
            }

            if (this.faqProd().url) {
                this.showButton(true);
            }
        },
        initObservable: function () {
            this._super()
                .observe({
                    showButton: false
                });

            this.faqProd.subscribe(function(product){
                this.showButton(!!product.url);
            }.bind(this));

            return this;
        },
        redirectToProduct: function () {
            window.location = this.faqProd().url;
        }
    });
});
