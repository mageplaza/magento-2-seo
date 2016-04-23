<?php

namespace Mageplaza\Seo\Block\Page\Head;

use Mageplaza\Seo\Block\Abstractt;

class Product extends Abstractt
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

    public function getRegistry($code)
    {
        return $this->registry->registry($code);
    }
    public function getCurrency(){
        return $this->storeManager->getStore()->getCurrentCurrencyCode();

    }

    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }
}