var config = {
    paths: {
        jquerymin: 'Dividebuy_CheckoutConfig/js/jquery.min',
        scrollbarmin: 'Dividebuy_CheckoutConfig/js/customscrollbar.concat.min',
        bootstrapmin: 'Dividebuy_CheckoutConfig/js/bootstrap.min',
        dividebuy: 'Dividebuy_CheckoutConfig/js/dividebuy',
        jsPostcodes: 'Dividebuy_CheckoutConfig/js/jspostcode',
    },
    shim: {
        'jquerymin': {
            deps: ['jquery']
        },
        'scrollbarmin': {
            deps: ['jquery']
        },
        'dividebuy': {
            deps: ['jquery']
        },
        'jsPostcodes': {
            deps: ['jquery']
        }
    }
};