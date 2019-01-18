// var jQuery = $.noConflict();
var showDevideByTP = false;

require(["jquery"], function($) {
    jQuery(document).on({
        mouseenter: function() {
            // Hover code
            var content = "<div class='slip_tooltip_container'><div class='slip_tooltip_content'><span class='tooltip-box'><span class='blue'>Divide</span><span class='grey'>Buy</span><b> - The Easy Interest-Free Checkout.</b><br/>By clicking on this you will be taken to our <b>secure platform</b> to fill out our <b>60 second application.</b> With us you'll get an <b>instant decision</b>, <b>96% acceptance rate</b> and a <b>choice of instalment options</b> to suit you.</span></div></div>";
            if (!(jQuery('body').find('.slip_tooltip_container').length)) {
                jQuery(content).appendTo('body').fadeIn('slow');
            }
        },
        mouseleave: function() {
            // Hover out code
            jQuery('.slip_tooltip_container').remove();
        }
    }, '.span_imgchange, .span_imgchange_new');
    mousemovebtn = function(evt, that) {
        // Mouse out code
        var tooltip_ele = jQuery(".slip_tooltip_container");
        var pageLeft = evt.pageX;
        var pageTop = evt.pageY;
        var windowWidth = jQuery(window).width();
        var windowHeight = jQuery(window).height();
        var tooltipWidth = tooltip_ele.width();
        var tooltipHeight = tooltip_ele.height();
        var scrollTop = jQuery(window).scrollTop();
        var tooltip_offset = tooltip_ele.offset();
        var tooltip_top = tooltip_offset.top;
        var offset = jQuery(that).offset();
        var span_top = jQuery(that).offset().top;
        if ((pageLeft + tooltipWidth + 30) > windowWidth) {
            pageLeft -= tooltipWidth;
            pageLeft -= 30;
        }
        //console.log(pageTop + "|" + jQuery(this).position().top + "|" + span_top + "|" + scrollTop + "|" + tooltipHeight);
        if ((span_top - scrollTop - 20) < tooltipHeight) {
            pageTop += 30;
        } else {
            pageTop -= (tooltipHeight + 30);
        }
        var mousex = pageLeft; //Get X coordinates
        var mousey = pageTop; //Get Y coordinates
        //console.log(mousex + mousey);
        jQuery('.slip_tooltip_container').css({
            top: mousey,
            left: mousex
        });
    }
    jQuery(document).on('mouseleave', "img.tooltip1", function() {
        jQuery(".tooltip-db").fadeOut('fast');
    });
});

// Displaying instalment details in cart and product page
function getInstalments(price, url, callback) {
    var startTime = new Date();
    jQuery.ajax({
        type: 'post',
        url: url,
        data: {
            price: price
        },
        beforeSend: showTooltipLoader,
        success: function(response) {
            if (jQuery('.modal-popup').hasClass('_show') || jQuery(".loader-set").is(":visible")) {
                return false;
            }
            var now = new Date();
            var difference = (now.getTime() - startTime.getTime()) / 1000;
            if (difference >= 3) {
                jQuery('#instalment-loader').removeClass('small-db-loader');
                jQuery(".instalment-details").html(response);
            } else {
                var seconds = 3000 - (difference * 1000);
                setTimeout(function() {
                    jQuery('#instalment-loader').removeClass('small-db-loader');
                    jQuery(".instalment-details").html(response);
                }, seconds);
            }
            // Removing loader class adding response
            // jQuery('#instalment-loader').removeClass('small-db-loader');
            // jQuery(".instalment-details").html(response);

            // if (typeof callback == 'function') {
            //     callback.apply(window);
            // }
        }
    });
}

// Function for displaying error message.
function displayErrorMessage(retailerName) {
    alert("An unexpected error has occurred. Please contact " + retailerName + " to let them know and complete your order.");
}

// Displaying DivideBuy modal when user click on DivideBuy button on checkout page.
function displayDividebuyModal(url, retailerName) {
    jQuery(".loader-set").show();
    jQuery(".product_page_instalments").html();
    jQuery.ajax({
        type: 'post',
        url: url,
        type: 'POST',
        success: function(data) {
            if (data !== "") {
                jQuery(".loader-set").hide();
                jQuery('#dividebuy-modal').modal('openModal');
                jQuery("#dividebuy-modal-container").html(data);
                modalRedirectionAccessibility();
            }
        },
        error: function() {
            jQuery('#dividebuy_modal').css('display', 'none');
            displayErrorMessage(retailerName);
            window.location.reload();
        }
    });
}

// Performing guest login
function guestLogin(url, retailerName) {
    jQuery("#shipping-loader").show();
    var startTime = new Date();
    jQuery.ajax({
        type: 'POST',
        url: url,
        success: function(response) {
            if (response !== "") {
                // jQuery("#dividebuy-modal-container").html(response);
                var now = new Date();
                var difference = (now.getTime() - startTime.getTime()) / 1000;
                if (difference >= 5) {
                    jQuery("#shipping-loader").hide();
                    jQuery("#dividebuy-modal-container").html(response);
                    modalRedirectionAccessibility();
                } else {
                    var seconds = 5000 - (difference * 1000);
                    setTimeout(function() {
                        jQuery("#shipping-loader").hide();
                        jQuery("#dividebuy-modal-container").html(response);
                    }, seconds);
                    modalRedirectionAccessibility();
                }
            }
        },
        error: function() {
            jQuery("#shipping-loader").hide();
            displayErrorMessage(retailerName);
            window.location.reload();
        }
    });
}

// Performing user login action.
function userLogin(email, password, url, retailerName) {
    if (email === "" && password === "") {
        jQuery("#inputValidationMsg").html("Email and password required.");
        return false;
    } else if (email === "" && password !== "") {
        jQuery("#inputValidationMsg").html("Email is required.");
        return false;
    } else if (password === "" && email !== "") {
        jQuery("#inputValidationMsg").html("Password is required.");
        return false;
    }

    jQuery("#shipping-loader").show();
    var startTime = new Date();
    var data = jQuery("#user_login_form").serialize();
    jQuery.ajax({
        type: 'post',
        url: url,
        data: data,
        success: function(response) {
            if (response !== "") {
                var now = new Date();
                var difference = (now.getTime() - startTime.getTime()) / 1000;
                if (difference >= 5) {
                    jQuery("#shipping-loader").hide();
                    jQuery("#dividebuy-modal-container").html(response);
                    modalRedirectionAccessibility();
                } else {
                    var seconds = 5000 - (difference * 1000);
                    setTimeout(function() {
                        jQuery("#shipping-loader").hide();
                        jQuery("#dividebuy-modal-container").html(response);
                    }, seconds);
                    modalRedirectionAccessibility();
                }
            } else {
                jQuery("#inputValidationMsg").html("Please check your email and password.");
                jQuery("#shipping-loader").hide();
            }
        },
        error: function() {
            jQuery("#shipping-loader").hide();
            displayErrorMessage(retailerName);
            window.location.reload();
        }
    });
}

// Displaying shipping modal after login.
function displayShippingModal(url, startTime, retailerName) {
    var startTime = (typeof startTime == 'undefined' ? 0 : startTime);
    jQuery.ajax({
        type: 'POST',
        url: url,
        success: function(response) {
            if (response !== "") {
                if (startTime != '') {
                    var now = new Date();
                    var difference = (now.getTime() - startTime.getTime()) / 1000;
                    if (difference >= 5) {
                        jQuery("#shipping-loader").hide();
                        jQuery("#dividebuy-modal-container").html(response);
                        jQuery("#modalopen").attr("aria-label", "Postcode Dialog open");
                        jQuery("#modalopen").focus();
                    } else {
                        var seconds = 5000 - (difference * 1000);
                        setTimeout(function() {
                            jQuery("#shipping-loader").hide();
                            jQuery("#dividebuy-modal-container").html(response);
                        }, seconds);
                        jQuery("#modalopen").attr("aria-label", "Postcode Dialog open");
                        jQuery("#modalopen").focus();
                    }
                    jQuery("#modalopen").focus();
                } else {
                    jQuery("#shipping-loader").hide();
                    jQuery("#dividebuy-modal-container").html(response);
                }
            }
        },
        error: function() {
            jQuery("#shipping-loader").hide();
            displayErrorMessage(retailerName);
            window.location.reload();
        }
    });
}

// Getting shipping estimate based on entered zipcode.  
function getShippingEstimate(postcode, url, retailerName) {
    if (postcode === "") {
        jQuery("#postcode_error").html("Please enter postcode");
        jQuery("#all_shipping_methods").html("");
        return false;
    } else if (!checkPostCode(postcode)) {
        jQuery("#postcode_error").html("Please enter valid postcode");
        jQuery("#all_shipping_methods").html("");
        return false;
    } else {
        jQuery("#postcode_error").html("");
        jQuery(".custom-modal").addClass("estimate-modal");
    }
    jQuery("#shipping-loader").show();
    var startTime = new Date();
    jQuery.ajax({
        type: 'POST',
        url: url,
        data: {
            user_postcode: postcode
        },
        success: function(response) {
            if (response !== "") {

                var now = new Date();
                var difference = (now.getTime() - startTime.getTime()) / 1000;
                if (difference >= 5) {
                    jQuery("#shipping-loader").hide();
                    jQuery("#all_shipping_methods").html(response);
                } else {
                    var seconds = 5000 - (difference * 1000);
                    setTimeout(function() {
                        jQuery("#shipping-loader").hide();
                        jQuery("#all_shipping_methods").html(response);
                    }, seconds);
                }
            } else {

                jQuery("#postcode_error").html("Shipping is not available for entered pincode.");
            }
        },
        error: function() {
            jQuery("#shipping-loader").hide();
            displayErrorMessage(retailerName);
            window.location.reload();
        }
    });
}

function placeNewOrder(zipcode, shippingMethod, url, userEmail, retailerName) {
    jQuery.ajax({
        type: 'POST',
        //showLoader: true,
        url: url,
        data: {
            postcode: zipcode,
            shipping_method: shippingMethod
        },
        success: function(response) {
            if ("postcode" in response && response.postcode === false) {
                displayDividebuyModal(response.redirecturl);
                return false;
            }
            if (response.redirecturl !== "") {
                window.location.href = response.redirecturl;
            }
            if (response.message !== "") {
                alert("Something went wrong.");
                window.location.href = response.carturl;
            }
            // jQuery(".loader-set").hide();
        },
        error: function() {
            jQuery("#shipping-loader").hide();
            displayErrorMessage(retailerName);
            window.location.reload();
        }
    });
}
// tooltip
function showTooltip() {
    var tooltipAnchorImage = document.getElementById('dividebuy_image_after_cart');
    var windowInnerHeight = window.innerHeight;
    var tooltipAnchorBottom = tooltipAnchorImage.getBoundingClientRect().bottom;
    var tooltipContentWrapperHeight = 0;
    var tooltipPosition = 'bottom';
    document.querySelector('.product_page_instalments').style.display = 'block';
    tooltipContentWrapperHeight = Math.round(parseInt(document.querySelector('.product_page_instalments').clientHeight, 10));
    document.querySelector('.product_page_instalments').style.display = 'none';
    if (tooltipContentWrapperHeight + tooltipAnchorBottom > windowInnerHeight) {
        tooltipPosition = 'top';
    }
    if (tooltipPosition == 'bottom') {
        jQuery('.product_page_instalments').css({
            'top': tooltipAnchorImage.clientHeight + 10
        });
    } else {
        jQuery('.product_page_instalments').css({
            'top': -tooltipContentWrapperHeight
        });
    }

    if (showDevideByTP) {
        jQuery(".tooltip-db").fadeIn('fast');
    }
}

function showTooltipLoader() {
    //jQuery('.product_page_instalments').html("loading...");
    jQuery(".tooltip-db-1").removeClass("dnone");
    jQuery('#instalment-loader').addClass('small-db-loader');
    var tooltipAnchorImage = document.getElementById('dividebuy_image_after_cart');
    var windowInnerHeight = window.innerHeight;
    var tooltipAnchorBottom = tooltipAnchorImage.getBoundingClientRect().bottom;
    var tooltipContentWrapperHeight = 0;
    var tooltipPosition = 'bottom';
    document.querySelector('.product_page_instalments').style.display = 'block';
    tooltipContentWrapperHeight = Math.round(parseInt(document.querySelector('.product_page_instalments').clientHeight, 10));
    document.querySelector('.product_page_instalments').style.display = 'none';
    if (tooltipContentWrapperHeight + tooltipAnchorBottom > windowInnerHeight) {
        tooltipPosition = 'top';
    }
    if (tooltipPosition == 'bottom') {
        jQuery('.product_page_instalments').css({
            'top': tooltipAnchorImage.clientHeight + 10
        });
    } else {
        jQuery('.product_page_instalments').css({
            'top': -tooltipContentWrapperHeight
        });
    }
    jQuery(".tooltip-db").fadeIn('fast');
}

//Accessbility Script
document.addEventListener("keyup", function(event) {
    if (event.which === 9) {
        if (jQuery("body").hasClass("_has-modal") === true) {
            trapTabKey(jQuery('#dividebuy_modal'), event);
        }
    }
});
var focusableElementsString = "a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, *[tabindex], *[contenteditable]";

// store the item that has focus before opening the modal window
var focusedElementBeforeModal;

function trapTabKey(obj, evt) {

    // if tab or shift-tab pressed
    if (evt.which == 9) {
        // get list of all children elements in given object
        var o = obj.find('*');

        // get list of focusable items
        var focusableItems;
        focusableItems = o.filter(':visible');

        // get currently focused item
        var focusedItem;
        focusedItem = jQuery(':focus');

        // get the number of focusable items
        var numberOfFocusableItems;
        numberOfFocusableItems = focusableItems.length;

        // get the index of the currently focused item
        var focusedItemIndex;
        focusedItemIndex = focusableItems.index(focusedItem);

        if (evt.shiftKey) {
            //back tab
            // if focused on first item and user preses back-tab, go to the last focusable item
            if (focusedItemIndex == 0) {
                focusableItems.get(numberOfFocusableItems - 1).focus();
                evt.preventDefault();
            } else if (focusedItemIndex == -1) {
                if (jQuery('#cookie').length != 0) {
                    jQuery('#cookie').focus();
                } else if (jQuery('#last-item-dividebuy').length != 0) {
                    jQuery('#last-item-dividebuy').focus();
                } else if (jQuery('#dividebuy_checkout_btn').length != 0) {
                    jQuery('#dividebuy_checkout_btn').focus();
                } else if (jQuery('#shipping-note').length != 0) {
                    jQuery('#shipping-note').focus();
                }
                evt.preventDefault();
            }

        } else {            
            //forward tab
            // if focused on the last item and user preses tab, go to the first focusable item
            if (focusedItemIndex == numberOfFocusableItems - 1) {
                focusableItems.get(0).focus();
                evt.preventDefault();
            } else if (focusedItemIndex == -1) {
                jQuery('#dividebuy_modal').focus();
                evt.preventDefault();
            }
        }
    }

}

function setFocusToFirstItemInModal(obj) {
    // get list of all children elements in given object
    var o = obj.find('*');

    console.log(focusableElementsString);
    console.log(o.filter(':visible').first());
    // set the focus to the first keyboard focusable item
    o.filter(':visible').first().focus();
}

// Modal dialog Redirection text function for accessibility 
function modalRedirectionAccessibility() {
    if (jQuery('#last-item-dividebuy').length != 0) {
        jQuery("#modalopen").attr("aria-label", "Mixed Cart Dialog open");
    } else if (jQuery('#shipping-note').length != 0) {
        jQuery("#modalopen").attr("aria-label", "Postcode Dialog open");
    }
    jQuery("#modalopen").focus();
}