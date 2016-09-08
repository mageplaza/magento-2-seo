<?php

namespace Mageplaza\Seo\Block\Page\Head;

use Mageplaza\Seo\Block\Abstractt;

class Page extends Abstractt
{
    public function getGeneralConfig($code)
    {
        return $this->helperData->getGeneralConfig($code);
    }

    public function getCurrentUrl()
    {
        $url = $this->objectManager
            ->get('Magento\Framework\UrlInterface');

        return $url->getCurrentUrl();
    }

    public function getCurrentCms()
    {
        return $this->registry->registry('cms_page');

    }

    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }
}