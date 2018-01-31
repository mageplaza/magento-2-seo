<?php
/**
 * Created by PhpStorm.
 * User: nghia
 * Date: 25/01/2018
 * Time: 09:09
 */

namespace Mageplaza\Seo\Plugin\Helper;

use Mageplaza\Seo\Helper\Data as HelperData;

class CanUseCanonicalTagForCategories
{
    protected $_helper;

    function __construct
    (
        HelperData $helper
    )
    {
        $this->_helper = $helper;
    }

    public function afterCanUseCanonicalTag(\Magento\Catalog\Helper\Category $category, $result)
    {
        if ($this->_helper->isEnabled()) {
            return $this->_helper->getDuplicateConfig('category_canonical_tag');
        }
        return $result;
    }
}