<?php

namespace Mageplaza\Seo\Block\Page\Head;

use Mageplaza\Seo\Block\AbstractSeo;

class Product extends AbstractSeo
{

    /**
     * get product image
     * @return mixed
     */
    public function getProductImage()
    {

        $imageUrl = $this->objectManager->get('Magento\Catalog\Helper\Image')
            ->init($this->getProduct(), 'product_base_image')
            ->getUrl();

        return $imageUrl;
    }


    /**
     * check entity enable
     * @return bool
     */
    public function entityEnable()
    {
        return $this->hreflang->hasEnableForEntity('enable_product');
    }


    /**
     * get Open graph description
     * @return string
     */
    public function getOgDescription()
    {
        $product = $this->getProduct();

        /**
         * Default information from meta description or short description (stripped)
         */
        $description = $product->getMetaDescription() ? $product->getMetaDescription() : $product->getShortDescription();

        /**
         * if exist og description -> send to frontend
         */
        if ($ogDescription = $product->getData('mp_seo_og_description')) {
            $description = $ogDescription;
        }
        $description = strip_tags($description);
        return $description;
    }
}
