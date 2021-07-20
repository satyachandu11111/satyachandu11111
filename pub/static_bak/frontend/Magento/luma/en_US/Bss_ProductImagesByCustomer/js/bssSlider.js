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
    "mage/mage",
    "Bss_ProductImagesByCustomer/js/OwlCarousel2-2.2.1/dist/owl.carousel"
], function($) {
    return function (config) {

        function caculatorNavShow()
        {
            var horizontalImage = config.bssWidthAItem,
                contentWidth = $('.columns').width(),
                widthBorder = 1,
                marginRight = 10,
                numberItem = parseInt(contentWidth / ( horizontalImage + (widthBorder * 2) + (marginRight) ));

            var max = 0;
            if (config.bssNumberImages < config.bssNumberImagesApprove) {
                max = config.bssNumberImages;
            } else {
                max = config.bssNumberImagesApprove;
            }

            if (numberItem >= max) {
                return false;
            } else {
                return true;
            }

        }

        function caculatorSlider() {
            var horizontalImage = config.bssWidthAItem,
                contentWidth = $('.columns').width(),
                widthBorder = 1,
                marginRight = 10,
                numberItem = parseInt(contentWidth / ( horizontalImage + (widthBorder * 2) + (marginRight) ));

            var max = 0;

            if (config.bssNumberImages < config.bssNumberImagesApprove) {
                max = config.bssNumberImages;
            } else {
                max = config.bssNumberImagesApprove;
            }

            if (numberItem > max) {
                numberItem = max;
            }

            var widthAItem = ( horizontalImage + (widthBorder * 2) + (marginRight) );
            var widthSlider = (widthAItem * (numberItem)) - 10;

            $('.bssContainerSliderAndForm ul.bss_product_images_slider').width(widthSlider);
        }
        $(document).ready(function () {
            var bssSpeedOri = parseInt(config.bssSpeedSlider);
            if (bssSpeedOri < 1) {
                bssSpeedOri = 1;
            }
            var bssSpeed = 1000 * bssSpeedOri;
            $('ul.owl-carousel').owlCarousel({
                'loop' : false,
                'margin': 10,
                'nav' : caculatorNavShow(),
                'dots' : false,
                'navText' : ["pre","next"],
                'autoWidth' : true,
                'autoplay' : true,
                'autoplayTimeout' : bssSpeed
            });
            caculatorSlider();

            $(window).on('resize', function(){
                caculatorSlider();
            });
        });
    }
});
