/**
 * Theiconnzz
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the kota-factory.com license that is
 * available through the world-wide-web at this URL:
 * https://www.kota-factory.com
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Theiconnz
 * @package   Theiconnz_Campaign
 * @copyright Copyright (c) KOTA-FACTORY (https://www.kota-factory.com/)
 * @license   https://www.kota-factory.com
 */
define([
    'Magento_Ui/js/modal/alert',
    "jquery",
    "jquery/ui",
    'mage/validation'
], function (alert, $) {
    "use strict";

    $.widget(
        'theiconnz.campaign', {
        options: {
            validationURL: '',
            fileuploadid: "#file-upload",
            file2uploadid: "#file-upload-2",
            formelement: "#campaign_form",
            successblock: "#campaign_form",
            formcontainer: ".campaign-data-form",
            formid: "campaign_form",
            hideonsuccess: ".success_hide",
            fileuploadmandatory: "0",
            file2uploadmandatory: "0",
            fileuploaderrormessage: "File upload is mandatory"
        },

        /** @inheritdoc */
        _create: function () {
            this._on(this.element, {
                'submit': this.onSubmit
            });
        },

        /**
         * Validates requested form.
         *
         * @return {Boolean}
         */
        isValid: function () {
            return this.element.validation() && this.element.validation('isValid');
        },

        /**
         * Validates updated shopping cart data.
         *
         * @param {String} url - request url
         * @param {Object} data - post data for ajax call
         */
        validateItems: function (url, data) {
            $.extend(data, {
                'form_key': $.mage.cookies.get('form_key'),
            });

            var form = $(this.options.formelement);
            var formdata = new FormData(form[0]);
            var fileuploadid = $(this.options.fileuploadid);
            var file2uploadid = $(this.options.file2uploadid);

            if(fileuploadid[0]!=undefined) {
                var files = fileuploadid[0].files;
                var file = files[0];


                if (file != undefined) {
                    formdata.append('filename', file, file.name);
                } else {
                    if (this.options.fileuploadmandatory!="0") {
                        $('').append('<div id="filename-error" class="mage-error">'+this.options.fileuploaderrormessage+'</div>')
                        return false;
                    }

                }
            }


            if(file2uploadid[0]!=undefined) {
                var files = file2uploadid[0].files;
                var file = files[0];

                if (file != undefined) {
                    formdata.append('filename_2', file, file.name);
                } else {
                    if (this.options.file2uploadmandatory!="0") {
                        $('').append('<div id="filename-error" class="mage-error">'+this.options.fileuploaderrormessage+'</div>')
                        return false;
                    }
                }
            }


            $.ajax({
                url: url,
                data: formdata,
                type: 'POST',
                dataType: 'json',
                context: this,
                processData: false,
                contentType: false,
                enctype: 'multipart/form-data',

                /** @inheritdoc */
                beforeSend: function () {
                    $(document.body).trigger('processStart');
                },

                /** @inheritdoc */
                complete: function () {
                    $(document.body).trigger('processStop');
                }
            })
            .done(function (response) {
                if (response.success) {
                    this.onSuccess();
                } else {
                    this.onError(response);
                }
            })
            .fail(function () {
                this.onError('Campaigns submit failed.');
            });
        },

        /**
         * Form validation succeed.
         */
        onSuccess: function () {
            $(this.options.formcontainer).hide();
            $(this.options.hideonsuccess).hide();
            $(this.options.successblock).show();
            document.getElementById(this.options.formid).reset();
        },

        /**
         * Form validation failed.
         */
        onError: function (response) {
            if (response['error_message']) {
                alert({
                    content: response['error_message']
                });
            }
        },

        /**
         * Prevents default submit action and calls form validator.
         *
         * @param {Event} event
         * @return {Boolean}
         */
        onSubmit: function (event) {
            event.preventDefault();
            if (!this.options.validationURL) {
                return true;
            }

            if (this.isValid()) {
                this.validateItems(this.options.validationURL, this.element.serialize());
            }

            return false;
        },

   });

    return $.theiconnz.campaign;
});
