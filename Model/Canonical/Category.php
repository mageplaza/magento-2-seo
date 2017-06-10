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
 * @copyright   Copyright (c) 2017 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Model\Canonical;

use \Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;

class Category
{

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;


	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $objectManager;

	public function __construct(
		Registry $registry,
		ObjectManagerInterface $objectManager

	)
	{
		$this->registry      = $registry;
		$this->objectManager = $objectManager;

	}

	/**
	 * get canonical url
	 * @return null|string
	 */
	public function getCanonicalUrl()
	{
		$currentCategory = $this->registry->registry('current_category');

		if ($currentCategory == null) return null;

		$urlObject = $this->objectManager
			->get('Magento\Framework\UrlInterface');
		$url       = $urlObject->getCurrentUrl();

		/**
		 * clean up param:
		 * - layered navigation: ?price=200-300
		 * - category: ?p=2
		 */
		$position = strpos($url, '?');
		if ($position !== false) {
			$url = substr($url, 0, $position);
		}


		return $url;


	}
}