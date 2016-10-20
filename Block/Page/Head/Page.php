<?php

namespace Mageplaza\Seo\Block\Page\Head;

use Mageplaza\Seo\Block\AbstractSeo;

class Page extends AbstractSeo
{
    /**
     * get general config
     * @param $code
     *
     * @return mixed
     */
    public function getGeneralConfig($code)
    {
        return $this->helperData->getGeneralConfig($code);
    }


    /**
     * get current cms object
     * @return mixed
     */
    public function getCurrentCms()
    {
        return $this->registry->registry('cms_page');

    }

    /**
     * get current category object
     * @return mixed
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }
}