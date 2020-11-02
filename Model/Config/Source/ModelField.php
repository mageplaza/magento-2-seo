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
 * Class ModelField
 * @package Mageplaza\Seo\Model\Config\Source
 */
class ModelField implements ArrayInterface
{
    const GTIN8  = 'gtin8';
    const GTIN13 = 'gtin13';
    const GTIN14 = 'gtin14';
    const MPN    = 'mpn';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('gtin8'),
                'value' => self::GTIN8
            ],
            [
                'label' => __('gtin13'),
                'value' => self::GTIN13
            ],
            [
                'label' => __('gtin14'),
                'value' => self::GTIN14
            ],
            [
                'label' => __('mpn'),
                'value' => self::MPN
            ]
        ];
    }
}
