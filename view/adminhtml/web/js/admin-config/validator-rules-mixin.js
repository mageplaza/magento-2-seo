/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Seo
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery'
], function ($) {
    'use strict';
    return function (target) {
        $.validator.addMethod(
            'validate-phone-number',
            function (value) {
                var regex = new RegExp(/^\+(?:\d(?:\(\d{3}\)|-\d{3})-\d{3}-(?:\d{2}-\d{2}|\d{4})|\d{11})$/);
                if (value.length) {
                    return regex.match(value);
                }
                return true;
            },
            $.mage.__('Please enter a valid phone number')
        );
        return target;
    };
});