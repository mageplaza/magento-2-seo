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
 * Class ReturnMethod
 *
 * @package Mageplaza\Seo\Model\Config\Source
 */
class ReturnMethod implements OptionSourceInterface
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
                'label' => __('Keep Product'),
                'value' => 'KeepProduct'
            ],
            [
                'label' => __('Return At Kiosk'),
                'value' => 'ReturnAtKiosk'
            ],
            [
                'label' => __('Return By Mail'),
                'value' => 'ReturnByMail'
            ],
            [
                'label' => __('Return In Store'),
                'value' => 'ReturnInStore'
            ]
        ];

        return $options;
    }
}
