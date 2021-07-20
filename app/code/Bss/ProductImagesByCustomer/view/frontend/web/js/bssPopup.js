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
define([
    "jquery",
    'Bss_ProductImagesByCustomer/js/fancybox/dist/jquery.fancybox'
], function($) {
    $(document).ready(function () {
        $('[data-fancybox="images"]').fancybox({
            loop : true,
            keyboard : true,
            smallBtn : 'auto',
            protect : true,
            animationEffect : "zoom",
            zoomOpacity : "auto",
            buttons : [
                'slideShow',
                'fullScreen',
                'thumbs',
                'zoom',
                'close'
            ]
        });
    });
});
