require([
    'Magento_Customer/js/customer-data'
], function (customerData) {
    this.faqProd = customerData.get('faq_product');
    this.faqProd().url = window.location.href;
    customerData.set('faq_product', this.faqProd());
});
