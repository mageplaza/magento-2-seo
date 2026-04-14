<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Seo
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ReturnFees
 *
 * @package Mageplaza\Seo\Model\Config\Source
 */
class ReturnFees implements OptionSourceInterface
{
    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => '',
                'label' => __('-- Please Select --')
            ],
            [
                'label' => __('Free Return'),
                'value' => 'FreeReturn'
            ],
            [
                'label' => __('Original Shipping Fees'),
                'value' => 'OriginalShippingFees'
            ],
            [
                'label' => __('Restocking Fees'),
                'value' => 'RestockingFees'
            ],
            [
                'label' => __('Return Fees Customer Responsibility'),
                'value' => 'ReturnFeesCustomerResponsibility'
            ],
            [
                'label' => __('Return Shipping Fees'),
                'value' => 'ReturnShippingFees'
            ]
        ];

        return $options;
    }
}
