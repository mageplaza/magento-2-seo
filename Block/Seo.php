<?php

namespace Mageplaza\Seo\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Seo\Helper\Data as HelperData;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;

class Seo extends AbstractSeo
{

    /**
     * get sitelink app
     * @return mixed
     */
    public function getSitelinksApp()
    {
        return $this->helperData->getGeneralConfig('sitelinks_app');
    }

    /**
     * get sitelink app code
     * @return mixed
     */
    public function getSitelinksAppCode()
    {
        return $this->helperData->getGeneralConfig('sitelinks_app_code');
    }


    /**
     * get sitelink domain for app
     * @return string
     */
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