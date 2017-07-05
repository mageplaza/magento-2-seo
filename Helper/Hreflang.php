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
 * @package     Mageplaza
 * @copyright   Copyright (c) 2016 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Helper;

class Hreflang
{
    const ENABLE_PRODUCT = 'enable_product';
    const ENABLE_CATEGORY = 'enable_category';
    const ENABLE_PAGE = 'enable_page';


    protected $_helper;

    public function __construct(
        \Mageplaza\Seo\Helper\Data $helper
    ) {
    
        $this->_helper = $helper;
    }

    public function hasEnableHreflangUrl()
    {
        $enableHreflangUrl = $this->_helper->getHreflang('enable_hreflang_url');
        if ($enableHreflangUrl) {
            return true;
        }

        return false;
    }

    public function hasEnableForEntity($entity = null)
    {
        if (!$this->hasEnableHreflangUrl()) {
            return false;
        }
        if ($this->_helper->getHreflang($entity)) {
            return true;
        }

        return false;
    }

    public function addCountryCode()
    {
        if ($this->_helper->getHreflang('add_country_code')) {
            return true;
        }

        return false;
    }

    public function getXDeFault()
    {
        return $this->_helper->getHreflang('x_default');
    }
}
