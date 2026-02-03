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
 * Class ReturnLabelSource
 *
 * @package Mageplaza\Seo\Model\Config\Source
 */
class ReturnLabelSource implements OptionSourceInterface
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
                'label' => __('Return Label Customer Responsibility'),
                'value' => 'ReturnLabelCustomerResponsibility'
            ],
            [
                'label' => __('Return Label Download And Print'),
                'value' => 'ReturnLabelDownloadAndPrint'
            ],
            [
                'label' => __('Return Label InBox'),
                'value' => 'ReturnLabelInBox'
            ]
        ];

        return $options;
    }
}
