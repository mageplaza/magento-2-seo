<?php

namespace Mageplaza\Seo\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Seo\Helper\Data as HelperData;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;
class Richsnippets extends Template
{
	protected $storeManager;
	protected $helperData;
	protected $objectFactory;
	protected $checkoutSession;

	public function __construct(
		Context $context,
		HelperData $helperData,
		ObjectManagerInterface $objectManager,
		StoreManagerInterface $storeManager,
		Session $session,
		array $data = []
	) {
		$this->helperData      = $helperData;
		$this->objectManager   = $objectManager;
		$this->storeManager    = $storeManager;
		$this->checkoutSession = $session;
		parent::__construct($context, $data);
	}
	public function getHelper()
	{
		return $this->helperData;
	}
	public function getLogo()
	{
//		return $this->getSkinUrl(Mage::getStoreConfig('design/header/logo_src'));
	}


	public function getProfiles()
	{
		$config = $this->helperData;
		$profiles = trim($config->getRichsnippetsConfig('social_profiles'));
		$lines = '';
		if(!empty($profiles)){
			$profiles = explode("\n",$profiles);
			foreach($profiles as $_profile){
				$lines .= '"' . trim($_profile) . '",' . "\n";
			}
		}
		return $lines;

	}
}