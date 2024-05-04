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
 * Class CanonicalFor
 * @package Mageplaza\Seo\Model\Config\Source
 */
class CanonicalFor implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('--Please select--'), 'value' => '0'],
            ['label' => __('Category'), 'value' => '1'],
            ['label' => __('Product'), 'value' => '2']
        ];
    }
}
