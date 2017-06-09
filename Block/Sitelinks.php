<?php

namespace Mageplaza\Seo\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Seo\Helper\Data as HelperData;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;

class Sitelinks extends Seo
{
	protected $_template = 'sitelinks.phtml';

	/**
	 * get rich snippet config
	 * @param $code
	 *
	 * @return mixed
	 */
	public function getRichsnippetsHelper($code)
	{
		return $this->helperData->getRichsnippetsConfig($code);
	}
}