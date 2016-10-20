<?php

namespace Mageplaza\Seo\Block\Richsnippets;

use Mageplaza\Seo\Block\Richsnippets;

class Product extends Richsnippets
{


    /**
     * get review collection
     * @return mixed
     */
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

    /**
     * get review count
     * @return mixed
     */
    public function getReviewCount()
    {
        $product = $this->getProduct();
        if ( ! $product->getRatingSummary()) {
            $this->reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        }

        return $product->getRatingSummary()->getReviewsCount();
    }

    /**
     * get rating summary
     * @return mixed
     */
    public function getRatingSummary()
    {
        $product = $this->getProduct();
        if ( ! $product->getRatingSummary()) {
            $this->reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        }

        return $product->getRatingSummary()->getRatingSummary();
    }
}
