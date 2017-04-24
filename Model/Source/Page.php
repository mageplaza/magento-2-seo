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

use Magento\Cms\Model\PageFactory;

class Page
{
	protected $_pageFactory;

	public function __construct(
		PageFactory $pageFactory
	)
	{
		$this->_pageFactory = $pageFactory;
	}

	public function getStoreCollection()
	{
		return $this->_pageFactory->create()->getCollection();
	}


	public function toOptionArray()
	{
		$arr = [];
		foreach ($this->getStoreCollection() as $item) {
			$arr[] = ['value' => $item->getIdentifier(), 'label' => $item->getTitle()];
		}

		return $arr;
	}
}