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
 * @package     Mageplaza
 * @copyright   Copyright (c) 2016 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Model\Source;

class TitleSeparator implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$separators = [];
		foreach ($this->toArray() as $k=>$v)
		{
			$separators[] = ['value' => $k, 'label' => $v];
		}
		return $separators;
	}

	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			0 => __('.'),
			1 => __('✰'),
			2 => __('-'),
			3 => __('_'),
			4 => __('*'),
			5 => __('«'),
			6 => __('»'),
			7 => __('●'),
			8 => __('>'),
			9 => __('<'),
			10 => __('~'),
			11 => __('|'),
		];
	}
}