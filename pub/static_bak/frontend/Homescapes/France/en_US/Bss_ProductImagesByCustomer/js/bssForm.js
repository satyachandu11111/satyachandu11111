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
    "mage/template",
    "mage/translate",
    "mage/mage"
], function($, mageTemplate) {
    return function (config) {
        //Form jquery
        var i = 1;
        const CONVERT_MB_TO_B = 1048576;
        var uploadMaxFileSize = CONVERT_MB_TO_B * config.uploadMaxFileSize;

        //Add input upload file
        $('#buttonMoreBss').click(function () {
            var template = mageTemplate('#bssFormMageTemplate');
            if (jQuery("#bssUploadImage0")[0].files && jQuery("#bssUploadImage0")[0].files[0] != undefined) {
                var srcIconLabel = $('.bssDivFileField label img').attr("src");
                var bssRowFileId = 'bssRowFileId'+i;
                var bssUploadImage = 'bssUploadImage'+i;
                var bssImageDisplay = 'bssImageDisplay'+i;
                var bssWarning = 'bssWarning'+i;

                var bssRowFileIdAfter = i - 1;
                var bssRowFileStringAfter = '#bssRowFileId' + bssRowFileIdAfter;
                var html = template({
                    data: {
                        id: bssRowFileId,
                        idImg: bssImageDisplay,
                        idSpan: bssWarning,
                        idInput: bssUploadImage
                    }
                });
                $(bssRowFileStringAfter).after(html);
                $('#numberFileUpload').attr('value', i+1);
                i++;
                if (i === config.numberFileUploadOnce) {
                    $(this).css("display", "none");
                    alert("You are not allowed uploading more than "+i+" images at once!");
                }
            } else {
                var bssWordLight = setInterval(function () {
                    jQuery("#bssWarning0").toggleClass("bssRedColor");
                }, 500);
            }

        });

        //Change small image display
        $(document).on("change", ".checkFile", function () {
            var idInputFile = $(this).attr("id");
            var idImgDisplay = '#bssImageDisplay' + idInputFile.substr(14);
            var idWarning = '#bssWarning' + idInputFile.substr(14);
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(idWarning).css('display', 'none');
                        $(idImgDisplay).css('display', 'block');
                        $(idImgDisplay).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            readURL(this);
        });

        //Check file upload
        jQuery('#buttonBssUploadFile').click(function (e) {
            var check = true;
            var fileSize = 0;
            $('.checkFile').each(function () {
                var uploadImage = $(this).val();

                if (typeof this.files[0] != 'undefined') {
                    fileSize = this.files[0].size;
                    var extension = uploadImage.split('.').pop().toLowerCase();
                    if ($.inArray(extension, ['png', 'gif', 'jpeg', 'jpg']) === -1 || fileSize > uploadMaxFileSize) {
                        check = false;
                    }
                }
            });
            if (check === false) {
                alert("Please try again! The maximum allowed size for upload images is "+config.uploadMaxFileSize+"MB");
                e.preventDefault();
            }
        });
    }
});
