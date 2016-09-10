<?php

namespace Mageplaza\Seo\Block\Richsnippets;

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

    public function getCurrency()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();

    }

    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getReviewCollection()
    {
        if (null === $this->reviewCollection) {
            $this->reviewCollection = $this->registry->create()->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $this->getProduct()->getId()
            )->setDateOrder();
        }

        return $this->reviewCollection;
    }

    public function getReviewCount()
    {
        $product = $this->getProduct();
        if ( ! $product->getRatingSummary()) {
            $this->reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        }

        return $product->getRatingSummary()->getReviewsCount();
    }

    public function getRatingSummary()
    {
        $product = $this->getProduct();
        if ( ! $product->getRatingSummary()) {
            $this->reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        }

        return $product->getRatingSummary()->getRatingSummary();
    }
}
