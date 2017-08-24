<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Seo
 * @copyright   Copyright (c) 2016 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Plugin;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\App\Request\Http;
use Mageplaza\Seo\Helper\Data as HelperData;
use Magento\Framework\App\ObjectManager;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Registry;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
/**
 * Class SeoBeforeRender
 * @package Mageplaza\Seo\Plugin
 */
class SeoRender
{
	const GOOLE_SITE_VERIFICATION = 'google-site-verification';
	const MSVALIDATE_01			  = 'msvalidate.01';
	const P_DOMAIN_VERIFY		  = 'p:domain_verify';
	const YANDEX_VERIFICATION	  = 'yandex-verification';
	const SHOP_BY_BRAND_EXTENSION = 'Mageplaza_Shopbybrand';

	/**
	 * @var \Magento\Framework\View\Page\Config
	 */
	protected $pageConfig;

	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $request;

	/**
	 * @var \Mageplaza\Seo\Helper\Data
	 */
	protected $helperData;

	/**
	 * @type \Magento\Framework\App\ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
	 */
	protected $stockItemRepository;

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var \Magento\Review\Model\ReviewFactory
	 */
	protected $reviewFactory;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_urlBuilder;

	/**
	 * seoRender constructor.
	 * @param \Magento\Framework\View\Page\Config $pageConfig
	 * @param \Magento\Framework\App\Request\Http $request
	 * @param \Mageplaza\Seo\Helper\Data $helpData
	 * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Review\Model\ReviewFactory $reviewFactory
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\UrlInterface $urlBuilder
	 */
	function __construct(
		PageConfig $pageConfig,
		Http $request,
		HelperData $helpData,
		StockItemRepository $stockItemRepository,
		Registry $registry,
		ReviewFactory $reviewFactory,
		StoreManagerInterface $storeManager,
		UrlInterface $urlBuilder

	) {

		$this->pageConfig          = $pageConfig;
		$this->request             = $request;
		$this->helperData          = $helpData;
		$this->objectManager       = ObjectManager::getInstance();
		$this->stockItemRepository = $stockItemRepository;
		$this->registry            = $registry;
		$this->_storeManager       = $storeManager;
		$this->reviewFactory       = $reviewFactory;
		$this->_urlBuilder         = $urlBuilder;
	}

	/**
	 * @param \Magento\Framework\View\Page\Config\Renderer $subject
	 */
	public function beforeRenderMetadata(\Magento\Framework\View\Page\Config\Renderer $subject)
	{

		$this->showVerifications();

		$pages = array(
			'catalogsearch_result_index',
			'cms_noroute_index',
			'catalogsearch_advanced_result'
		);
		if(in_array($this->getFullActionName(),$pages)){
			$this->pageConfig->setMetadata('robots','NOINDEX,NOFOLLOW');
		}

	}

	/**
	 * @param \Magento\Framework\View\Page\Config\Renderer $subject
	 * @param $result
	 * @return string
	 */
	public function afterRenderHeadContent(\Magento\Framework\View\Page\Config\Renderer $subject,$result)
	{
		$productStructuredData = '';
		if($this->getFullActionName() == 'catalog_product_view'){
			$productStructuredData = $this->showProductStructuredData();
		}
		return $result.$productStructuredData;
	}

	/**
	 *  Show verifications from config
	 */
	public function showVerifications(){
		$this->pageConfig->setMetadata(self::GOOLE_SITE_VERIFICATION,$this->helperData->getVerficationConfig('google'));
		$this->pageConfig->setMetadata(self::MSVALIDATE_01,$this->helperData->getVerficationConfig('bing'));
		$this->pageConfig->setMetadata(self::P_DOMAIN_VERIFY,$this->helperData->getVerficationConfig('pinterest'));
		$this->pageConfig->setMetadata(self::YANDEX_VERIFICATION,$this->helperData->getVerficationConfig('yandex'));
	}

	/**
	 * Get full action name
	 * @return string
	 */
	public function getFullActionName(){
		return $this->request->getFullActionName();
	}

	/**
	 * Get current product
	 * @return mixed
	 */
	public function getProduct()
	{
		return $this->registry->registry('current_product');
	}

	/**
	 * Get Url
	 * @param string $route
	 * @param array $params
	 * @return string
	 */
	public function getUrl($route = '', $params = [])
	{
		return $this->_urlBuilder->getUrl($route, $params);
	}

	/**
	 * Get product stock
	 * @param $productId
	 * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
	 */
	public function getProductStock($productId){
		return $this->stockItemRepository->get($productId);
	}

	/**
	 * Get review count
	 * @return mixed
	 */
	public function getReviewCount()
	{
		if (!$this->getProduct()->getRatingSummary()) {
			$this->getEntitySummary($this->getProduct());
		}
		return $this->getProduct()->getRatingSummary()->getReviewsCount();
	}

	/**
	 * Get rating summary
	 * @return mixed
	 */
	public function getRatingSummary()
	{
		if (!$this->getProduct()->getRatingSummary()) {
			$this->getEntitySummary($this->getProduct());
		}

		return $this->getProduct()->getRatingSummary()->getRatingSummary();
	}

	/**
	 * Get entity summary
	 * @param $product
	 */
	public function getEntitySummary($product){
		$this->reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
	}

	/**
	 * Get product brand
	 * @return null | Brand
	 */
	public function getProductBrand()
	{

		if (!$this->helperData->checkModuleActive(self::SHOP_BY_BRAND_EXTENSION)) {
			return null;
		}

		/** @type \Mageplaza\Shopbybrand\Helper\Data $helper */
		$brandHelper = $this->objectManager->create('Mageplaza\Shopbybrand\Helper\Data');

		if ($optionId = $this->getProduct()->getData($brandHelper->getAttributeCode())) {
			/** @type \Mageplaza\Shopbybrand\Model\Brand $brand */
			$brand = $this->objectManager->create('Mageplaza\Shopbybrand\Model\Brand');
			return $brand->loadByOption($optionId);
		}
		return null;
	}

	/**
	 * Show product structured data
	 * @return string
	 * @throws \Magento\Framework\Exception\InputException
	 *
	 * Learn more: https://developers.google.com/structured-data/rich-snippets/products#single_product_page
	 */
	public function showProductStructuredData(){

		if($currentProduct = $this->getProduct()) {
			try{
				$productStructuredData = array(
					'@context'    => 'http://schema.org/',
					'@type'       => 'Product',
					'name'        => $currentProduct->getName(),
					'description' => trim(strip_tags($currentProduct->getDescription())),
					'sku'         => $currentProduct->getSku(),
					'url'         => $currentProduct->getProductUrl(),
					'image'       => $this->getUrl('pub/media/catalog') . 'product' . $currentProduct->getImage(),
					'offers'      => array(
						'@type'           => 'Offer',
						'priceCurrency'   => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
						'price'           => $currentProduct->getPrice(),
						'priceValidUntil' => $currentProduct->getSpecialToDate(),
						'itemOffered'     => $this->getProductStock($currentProduct->getId())->getQty(),
						'availability'    => 'http://schema.org/' . $this->getProductStock($currentProduct->getId())->getIsInStock() ? 'InStock' : 'OutOfStock'
					)
				);
				if($brand = $this->getProductBrand()){
					$productStructuredData['brand']['@type'] = "Thing";
					$productStructuredData['brand']['name']  = $brand->getValue();

				}
				if($this->getReviewCount()){
					$productStructuredData['aggregateRating']['@type'] 		 = 'AggregateRating';
					$productStructuredData['aggregateRating']['bestRating']  = 100;
					$productStructuredData['aggregateRating']['worstRating'] = 0;
					$productStructuredData['aggregateRating']['ratingValue'] = $this->getRatingSummary();
					$productStructuredData['aggregateRating']['reviewCount'] = $this->getReviewCount();
				}
				return $this->helperData->createStructuredData($productStructuredData,'<!-- Product Structured Data by Mageplaza SEO-->');
			} catch (\Exception $e) {
				throw new InputException(__('Can not add structured data'));
			}
		}
	}

}
