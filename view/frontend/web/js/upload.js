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
    "jquery",
], function ($) {
    "use strict";

    $.widget(
        'theiconnz.upload', {
        options: {
            fileuploadcontent: "file-upload"
        },

        /** @inheritdoc */
        _create: function () {
            this._on(this.element, {
                'change': this.onChange
            });
        },

        /**
         * Prevents default submit action and calls form validator.
         *
         * @param {Event} event
         * @return {Boolean}
         */
        onChange: function (event) {
            if (this.element.length>0 && this.element[0].files[0]) {
                var filename = this.element[0].files[0].name;console.log(this.options.fileuploadcontent);
                $("#" + this.options.fileuploadcontent).addClass('c_color_light');
                var img = document.createElement('img');
                var src = URL.createObjectURL(event.target.files[0]);
                img.src = src;
                img.classList = 'c_uploaded_image';

                $("#" + this.options.fileuploadcontent).html('');
                $("#" + this.options.fileuploadcontent).append(img);
            }
        },

   });

    return $.theiconnz.upload;
});
