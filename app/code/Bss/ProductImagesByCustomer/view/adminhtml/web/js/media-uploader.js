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
    'jquery',
    'prototype',
    'mage/template',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'jquery/file-uploader'
], function ($, prototype, mageTemplate, alert) {
    'use strict';

    $.widget('bssProductImagesByCustomer.mediaUploader', {

        /**
         *
         * @private
         */
        _create: function () {
            var
                self = this,
                progressTmpl = mageTemplate('[data-template="uploader"]');

            this.element.find('input[type=file]').fileupload({
                dataType: 'json',
                formData: {
                    'form_key': window.FORM_KEY
                },
                dropZone: '[data-tab-panel=image-management]',
                sequentialUploads: true,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                maxFileSize: this.options.maxFileSize,

                /**
                 * @param {Object} e
                 * @param {Object} data
                 */
                add: function (e, data) {
                    var
                        fileSize,
                        tmpl;

                    $.each(data.files, function (index, file) {
                        fileSize = typeof file.size == 'undefined' ?
                            $.mage.__('We could not detect a size.') :
                            byteConvert(file.size);

                        data.fileId = Math.random().toString(33).substr(2, 18);

                        tmpl = progressTmpl({
                            data: {
                                name: file.name,
                                size: fileSize,
                                id: data.fileId
                            }
                        });

                        $(tmpl).appendTo(self.element);
                    });

                    $(this).fileupload('process', data).done(function () {
                        data.submit();
                    });
                },

                /**
                 * @param {Object} e
                 * @param {Object} data
                 */
                done: function (e, data) {
                    if (data.result && !data.result.error) {
                        self.element.trigger('addItem', data.result);
                    } else {
                        alert({
                            content: $.mage.__('We don\'t recognize or support this file extension type.')
                        });
                    }
                    self.element.find('#' + data.fileId).remove();
                },

                /**
                 * @param {Object} e
                 * @param {Object} data
                 */
                progress: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10),
                        progressSelector = '#' + data.fileId + ' .progressbar-container .progressbar';

                    self.element.find(progressSelector).css('width', progress + '%');
                },

                /**
                 * @param {Object} e
                 * @param {Object} data
                 */
                fail: function (e, data) {
                    var progressSelector = '#' + data.fileId;

                    self.element.find(progressSelector).removeClass('upload-progress').addClass('upload-failure')
                        .delay(2000)
                        .hide('highlight')
                        .remove();
                }
            });

            this.element.find('input[type=file]').fileupload('option', {
                process: [{
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png)$/
                }, {
                    action: 'resize',
                    maxWidth: this.options.maxWidth,
                    maxHeight: this.options.maxHeight
                }, {
                    action: 'save'
                }]
            });
        }
    });

    return $.bssProductImagesByCustomer.mediaUploader;
});
