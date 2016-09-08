<?php

namespace Mageplaza\Seo\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Seo\Helper\Data as HelperData;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;

class Seo extends Abstractt
{
    public function getGeneralConfig($code)
    {
        return $this->helperData->getGeneralConfig($code);
    }

    public function getSitelinksApp()
    {
        return $this->helperData->getGeneralConfig('sitelinks_app');
    }

    public function getSitelinksAppCode()
    {
        return $this->helperData->getGeneralConfig('sitelinks_app_code');
    }


    public function getSiteLinkDomainForApp()
    {
        $url = $this->getBaseUrl();
        $url = str_replace(
            array(
                ':',
                '//'
            ),
            array(
                '',
                '/'
            ),
            $url
        );

        return $url;
    }

}