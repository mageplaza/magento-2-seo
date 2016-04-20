<?php

namespace Mageplaza\Seo\Block\Richsnippets\Head;

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
	public function getProduct()
	{
		return $this->registry->registry('current_product');
	}
	public function getReviewCollection()
	{
//		if (null === $this->_reviewsCollection) {
//			$this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
//				->addStoreFilter(Mage::app()->getStore()->getId())
//				->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
//				->addEntityFilter('product', $this->getProduct()->getId())
//				->setDateOrder();
//		}
//
//		return $this->_reviewsCollection;
	}
	public function getReviewCount()
	{
//		$count = 0;
//		try {
//			$count = $this->getReviewCollection()->getSize();
//		} catch (Exception $e) {
//		}
//		return $count;
	}
	public function getRatingSummary()
	{
//		$aggregateRating = 100;
//		try {
//			if($this->getProduct()->getRatingSummary() && $this->getProduct()->getRatingSummary()){
//				$aggregateRating = $this->getProduct()->getRatingSummary()->getRatingSummary();
//			}
//		} catch (Exception $e) {
//		}
//		return $aggregateRating;

	}
}