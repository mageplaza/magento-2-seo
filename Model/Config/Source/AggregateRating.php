<?php
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

namespace Mageplaza\Seo\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class AggregateRating
 * @package Mageplaza\Seo\Model\Config\Source
 */
class AggregateRating implements ArrayInterface
{
    const ENABLE  = 1;
    const DISABLE = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Enable'),
                'value' => self::ENABLE
            ],
            [
                'label' => __('Disable'),
                'value' => self::DISABLE
            ]
        ];
    }
}
