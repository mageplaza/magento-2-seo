<?php

namespace Mageplaza\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Mageplaza\Seo\Helper\Data as SeoHelper;
use Magento\Framework\View\Page\Config;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Context;
use Magento\Framework\App\Request\Http as Url;
use Magento\Store\Model\Group;

class CmsPageLoadAfter implements ObserverInterface
{
	protected $helper;
	protected $pageConfig;
	protected $registry;
	protected $objectManager;
	protected $urlManager;
	protected $context;
	protected $url;
	protected $storeGroup;

	public function __construct(
		SeoHelper $helper,
		Config $pageConfig,
		Registry $registry,
		ObjectManagerInterface $objectManager,
		UrlInterface $urlManager,
		Context $context,
		Url $url,
		Group $storeGroup
	)
	{
		$this->helper        = $helper;
		$this->pageConfig    = $pageConfig;
		$this->registry      = $registry;
		$this->objectManager = $objectManager;
		$this->urlManager    = $urlManager;
		$this->context       = $context;
		$this->url           = $url;
		$this->storeGroup    = $storeGroup;
	}

	/**
	 * @param \Magento\Framework\Event\Observer $observer
	 *
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$action = $observer->getEvent()->getFullActionName();

		if ($action == 'cms_index_index') {

			$page = $observer->getData('page');

			$metaTitle = $this->helper->getGeneralConfig('meta_title');
			$metaDescription = $this->helper->getGeneralConfig('meta_description');

			if($metaTitle){
				$page->setMetaTitle($metaTitle);
			}
			if($metaDescription){
				$page->setMetaDescription($metaDescription);
			}

		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getBaseUrl()
	{
		return $this->objectManager->get(
			'Magento\Store\Model\StoreManagerInterface'
		)->getStore()->getBaseUrl();
	}

}