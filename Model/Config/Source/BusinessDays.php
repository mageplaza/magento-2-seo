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
 * Class BusinessDays
 *
 * @package Mageplaza\Seo\Model\Config\Source
 */
class BusinessDays implements OptionSourceInterface
{
    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Monday'),
                'value' => 'Monday'
            ],
            [
                'label' => __('Tuesday'),
                'value' => 'Tuesday'
            ],
            [
                'label' => __('Wednesday'),
                'value' => 'Wednesday'
            ],
            [
                'label' => __('Thursday'),
                'value' => 'Thursday'
            ],
            [
                'label' => __('Friday'),
                'value' => 'Friday'
            ],
            [
                'label' => __('Saturday'),
                'value' => 'Saturday'
            ],
            [
                'label' => __('Sunday'),
                'value' => 'Sunday'
            ],
        ];

        return $options;
    }
}
