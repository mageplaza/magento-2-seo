<?php

namespace Mageplaza\Seo\Block\Page\Head;

use Mageplaza\Seo\Block\AbstractSeo;

class Category extends AbstractSeo
{
    public function getDefaultContent()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function entityEnable()
    {
        return $this->hreflang->hasEnableForEntity('enable_category');
    }

    /**
     * get og category description
     * @return string
     */
    public function getCategoryDescription()
    {
        $category = $this->getCurrentCategory();
        $description  = strip_tags($category->getDescription());
        return $description;
    }
}
