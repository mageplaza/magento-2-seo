<?php

namespace Mageplaza\Seo\Block\Page\Head;
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
}