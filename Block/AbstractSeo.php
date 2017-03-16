<?php

namespace Mageplaza\Seo\Block;

use Magento\Framework\View\Element\Template;
use Mageplaza\Seo\Helper\Data as HelperData;
use Magento\Framework\ObjectManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Theme\Block\Html\Header\Logo;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Api\Data\StoreConfigInterface;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\ReviewFactory;
use Mageplaza\Seo\Helper\Hreflang;

class AbstractSeo extends Template
{
	const XML_PATH_GENERAL_LOCALE_CODE = 'general/locale/code';
	
	protected $hreflang;
	protected $objectManager;
	protected $helperData;
	protected $objectFactory;
	protected $checkoutSession;
	protected $logo;
	protected $registry;
	protected $reviewCollection;
	protected $reviewCollectionFactory;
	protected $reviewFactory;
	protected $reviewRederer;

	public function __construct(
		Hreflang $hreflang,
		Context $context,
		HelperData $helperData,
		ObjectManagerInterface $objectManager,
		Session $session,
		Registry $registry,
		Logo $logo,
		StoreConfigInterface $storeConfig,
		CollectionFactory $reviewCollectionFactory,
		ReviewFactory $reviewFactory,
		array $data = []
	)
	{
		$this->hreflang                = $hreflang;
		$this->helperData              = $helperData;
		$this->objectManager           = $objectManager;
		$this->checkoutSession         = $session;
		$this->registry                = $registry;
		$this->logo                    = $logo;
		$this->reviewCollectionFactory = $reviewCollectionFactory;
		$this->reviewFactory           = $reviewFactory;
		$this->storeConfig             = $storeConfig;
		parent::__construct($context, $data);
	}

	/**
	 * get seo helper
	 *
	 * @return \Mageplaza\Seo\Helper\Data
	 */
	public function getHelper()
	{
		return $this->helperData;
	}

	/**
	 * get config: business name
	 *
	 * @return mixed
	 */
	public function getBusinessName()
	{
		return $this->helperData->getInfoConfig('business_name');
	}


	/**
	 * get config: business phone
	 *
	 * @return mixed
	 */
	public function getBusinessPhone()
	{
		return $this->helperData->getInfoConfig('phone');
	}

	/**
	 * get config: business phone
	 * @return mixed
	 */
	public function getBusinessSalesPhone()
	{
		return $this->helperData->getInfoConfig('sales_phone');
	}

	/**
	 * get support phone number
	 * @return mixed
	 */
	public function getBusinessSupportPhone()
	{
		return $this->helperData->getInfoConfig('technical_support_phone');
	}


	/**
	 * get config: twitter account
	 *
	 * @return string
	 */
	public function getTwitterAccount()
	{
		$prefix  = '@';
		$account = $this->helperData->getSocialShares('twitter_account');

		return $prefix . $account;
	}

	/**
	 * get language code
	 *
	 * @return \Magento\Framework\Locale\Resolver
	 */
	public function getLangCode()
	{
		/** @var \Magento\Framework\ObjectManagerInterface $om */
		$om = $this->objectManager;
		/** @var \Magento\Framework\Locale\Resolver $resolver */
		$resolver = $om->get('Magento\Framework\Locale\Resolver');
		$resolver = strtolower($resolver);

		return $resolver;
	}

	/**
	 * get current canonical url
	 *
	 * @return mixed
	 */
	public function getCanonicalUrl()
	{

		$url = $this->getCurrentUrl();

		if ($this->helperData->getDuplicateConfig('https_canonical')) {
			$url = str_replace('http:', 'https:', $url);
		}

		return $url;
	}

	/**
	 * get current url
	 *
	 * @return mixed
	 */
	public function getCurrentUrl()
	{
		$urlObject = $this->objectManager
			->get('Magento\Framework\UrlInterface');
		$url       = $urlObject->getCurrentUrl();

		/**
		 * clean up param:
		 * - layered navigation: ?price=200-300
		 * - category: ?p=2
		 */
		if ($this->getDuplicateConfig('url_param')) {
			$position = strpos($url, '?');
			if ($position !== false) {
				$url = substr($url, 0, $position);
			}


		}

		return $url;
	}

	/**
	 * get general config
	 *
	 * @param $code
	 *
	 * @return mixed
	 */
	public function getGeneralConfig($code)
	{
		return $this->helperData->getGeneralConfig($code);
	}

	/**
	 * get duplicate content config
	 * @param $code
	 * @return mixed
	 */
	public function getDuplicateConfig($code)
	{
		return $this->helperData->getDuplicateConfig($code);

	}

	/**
	 * get registry value
	 *
	 * @param $code
	 *
	 * @return mixed
	 */
	public function getRegistry($code)
	{
		return $this->registry->registry($code);
	}


	/**
	 * get currency
	 *
	 * @return mixed
	 */
	public function getCurrency()
	{
		return $this->_storeManager->getStore()->getCurrentCurrencyCode();

	}

	/**
	 * get current product
	 *
	 * @return mixed
	 */
	public function getProduct()
	{
		return $this->registry->registry('current_product');
	}


	/**
	 * get current category
	 *
	 * @return mixed
	 */
	public function getCurrentCategory()
	{
		return $this->registry->registry('current_category');
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
	 * get base url
	 * @return mixed
	 */
	public function getBaseUrl()
	{
		return $this->objectManager->get(
			'Magento\Store\Model\StoreManagerInterface'
		)->getStore()->getBaseUrl();
	}

	public function getBaseDomain()
	{
		$baseUrl = $this->getBaseUrl();
		/**
		 * parser
		 */
	}


	/**
	 * Get Store name
	 *
	 * @return string
	 */
	public function getStoreName()
	{
		return $this->_storeManager->getStore()->getName();
	}


	/**
	 * Get current url for store
	 *
	 * @param bool|string $fromStore Include/Exclude from_store parameter from URL
	 * @return string
	 */
	public function getStoreUrl($fromStore = true)
	{
		return $this->_storeManager->getStore()->getCurrentUrl($fromStore);
	}


	/**
	 * Get website identifier
	 *
	 * @return string|int|null
	 */
	public function getWebsiteId()
	{
		return $this->_storeManager->getStore()->getWebsiteId();
	}

	/**
	 * Get Store code
	 *
	 * @return string
	 */
	public function getStoreCode()
	{
		return $this->_storeManager->getStore()->getCode();
	}

	/**
	 * @param null $storeId
	 * @return mixed|null
	 */
	public function getHrefLang($storeId=null)
	{
		if ($storeId == null)
			return null;

		return $this->helperData->getConfigValue(self::XML_PATH_GENERAL_LOCALE_CODE, $storeId);
	}

	/**
	 * @param $storeId
	 * @return string
	 */
	public function setHreflang($storeId)
	{
		if($this->hreflang->getXDeFault() == $storeId)
			return 'x-default';

		return str_replace('_','-',$this->getHrefLang($storeId));
	}

	/**
	 * @return string
	 */
	public function getStoreBaseUrl()
	{
		return $this->_urlBuilder->getUrl();
	}

	/**
	 * @return \Magento\Store\Api\Data\StoreInterface[]
	 */
	public function getAllStore()
	{
		return $this->_storeManager->getStores();
	}

	/**
	 * @return bool
	 */
	public function entityEnable()
	{
		return false;
	}

	/**
	 * @return array
	 */
	public function getUrls()
	{
		$arr = [];
		if(!$this->entityEnable())
			return $arr;
		
		foreach ($this->getAllStore() as $store) {
			$arr[] = [
				'href'     => str_replace($this->getStoreBaseUrl(), $store->getBaseUrl(), $this->getCanonicalUrl()),
				'hrefLang' => $this->setHreflang($store->getId())
			];
		}

		return $arr;
	}

}
