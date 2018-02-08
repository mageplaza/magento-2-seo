<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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
 * @copyright   Copyright (c) 2018 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Plugin\Helper;

use Mageplaza\Seo\Helper\Data as HelperData;

/**
 * Class CanUseCanonicalTagForCategories
 * @package Mageplaza\Seo\Plugin\Helper
 */
class CanUseCanonicalTagForCategories
{
    /**
     * @var \Mageplaza\Seo\Helper\Data
     */
    protected $_helper;

    /**
     * CanUseCanonicalTagForCategories constructor.
     * @param HelperData $helper
     */
    function __construct
    (
        HelperData $helper
    )
    {
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Catalog\Helper\Category $category
     * @param $result
     * @return mixed
     */
    public function afterCanUseCanonicalTag(\Magento\Catalog\Helper\Category $category, $result)
    {
        if ($this->_helper->isEnabled()) {
            return $this->_helper->getDuplicateConfig('category_canonical_tag');
        }

        return $result;
    }
}