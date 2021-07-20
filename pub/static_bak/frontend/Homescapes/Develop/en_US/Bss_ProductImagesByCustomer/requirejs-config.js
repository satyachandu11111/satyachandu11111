/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductImagesByCustomer
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
var config = {
    map: {
        '*': {
            bss_productimagesbycustomer: 'Bss_ProductImagesByCustomer/js/bssPopup',
            bss_productimagebycustomer_fancybox : 'Bss_ProductImagesByCustomer/js/fancybox/dist/jquery.fancybox',
            bss_productimagebycustomer_owlcarousel : 'Bss_ProductImagesByCustomer/js/OwlCarousel2-2.2.1/dist/owl.carousel'
        }
    },
    path: {
        'bss_productimagebycustomer_fancybox'  : 'Bss_ProductImagesByCustomer/js/fancybox/dist/jquery.fancybox',
        'bss_productimagebycustomer_owlcarousel' : 'Bss_ProductImagesByCustomer/js/OwlCarousel2-2.2.1/dist/owl.carousel'
    },
    shim:{
        'bss_productimagebycustomer_fancybox': {
            deps: ['jquery']
        },
        'bss_productimagebycustomer_owlcarousel': {
            deps: ['jquery']
        }
    }
};
require.config(config);
