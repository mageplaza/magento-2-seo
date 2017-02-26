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

class Language
{
	const EN = 0;
	const DE = 1;
	const ES = 2;
	const FR = 3;
	const OTHER  = 4;

	public function toOptionArray()
	{
		$options = [
			[
				'value' => self::DE,
				'label' => 'De'
			],
			[
				'value' => self::EN,
				'label' => 'En'
			],
			[
				'value' => self::ES,
				'label' => 'Es'
			],
			[
				'value' => self::FR,
				'label' => 'Fr'
			],
			[
				'value' => self::OTHER,
				'label' => 'Other'
			],
		];

		return $options;
	}
}