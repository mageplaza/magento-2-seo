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

class Product
{

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;

	public function __construct(
		Registry $registry
	)
	{
		$this->registry   = $registry;

	}

	/**
	 * @return string
	 */
	public function getCanonicalUrl()
	{
		$currentProduct = $this->registry->registry('current_product');

		if($currentProduct == null) return null;

		/**
		 * get real product url, without param
		 */

		$url = $currentProduct->getUrlModel()->getUrl($currentProduct);

		/**
		 *  Fix cocktail.html?gclid=ABCD
		 */
//		$position = strpos($url, '?');
//		if ($position !== false) {
//			$url = substr($url, 0, $position);
//		}

		return $url;

	}
}