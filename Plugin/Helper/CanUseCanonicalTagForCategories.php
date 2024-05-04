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

namespace Mageplaza\Seo\Plugin\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Helper\Category;
use Mageplaza\Seo\Helper\Data as HelperData;

/**
 * Class CanUseCanonicalTagForCategories
 * @package Mageplaza\Seo\Plugin\Helper
 */
class CanUseCanonicalTagForCategories
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var HelperData
     */
    protected $_helper;

    /**
     * CanUseCanonicalTagForCategories constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param HelperData $helper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        HelperData $helper
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper       = $helper;
    }

    /**
     * @param Category $category
     * @param $result
     *
     * @return mixed
     */
    public function afterCanUseCanonicalTag(Category $category, $result)
    {
        if ($this->_helper->isEnabled()) {
            return $this->_helper->canUseCanonicalForCategory($this->_storeManager->getStore()->getId());
        }

        return $result;
    }
}
