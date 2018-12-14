/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
define
require
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'Magento_Ui/js/modal/modal',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler,modal) {
    'use strict';

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 &&
            $('#co-shipping-method-form .fieldset.rates :checked').length === 0
        ) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }

    $('.cart-summary').mage('sticky', {
        container: '#maincontent'
    });

     var options = {    
                        type: 'popup',
                        responsive: true,
                        innerScroll: true,
                        title: $.mage.__('Write Your Own Review'),                      
                    };

        $(document).ready(function(){
                $('.review-fieldset').css('display','block');
                if ($(".block.review-add").length != 0) {
                    var popup = modal(options, $('.block.review-add'));
                    $(".reviews-actions > a.action.add , .review-popup").on('click',function(e){
                         e.preventDefault(); 
                        $(".block.review-add").trigger("openModal");
                    });
                }
                
            });

            $(".action-close").on('click',function(){ 
                    $(".block.review-add").trigger("closeModal");
            });

            $(document).on('click',function(e){ 
                   if($(e.target).hasClass('modals-overlay')) {
                      $(".block.review-add").trigger("closeModal");
                    }
            });

            jQuery(".modal-popup.modal-slide").each(function() {
                if (jQuery(this).find('.modal-content').is(':empty')){
                    jQuery(this).find('.modal-inner-wrap').css('display','none');
                }   
            });

    
    $('.panel.header > .header.links').clone().appendTo('#store\\.links');

    keyboardHandler.apply();
});
