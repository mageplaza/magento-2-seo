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

namespace Mageplaza\Seo\Plugin;

use Magento\Framework\App;

class Title extends \Magento\Framework\View\Page\Title
{

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	private $scopeConfig;

	protected $_titleSeparator;

	/**
	 * @param App\Config\ScopeConfigInterface $scopeConfig
	 */
	public function __construct(
		\Mageplaza\Seo\Model\Source\TitleSeparator $titleSeparator,
		App\Config\ScopeConfigInterface $scopeConfig
	)
	{
		$this->_titleSeparator = $titleSeparator;
		$this->scopeConfig     = $scopeConfig;
		parent::__construct($scopeConfig);
	}

	/**
	 * @param string $title
	 * @return string
	 */
	protected function addConfigValues($title)
	{
		$preparedTitle = $this->getTitlePrefix()
			. ($this->getTitlePrefix() ? ' ' . $this->getTitleSeparator() . ' ' : '')
			. $title
			. ($this->getTitleSuffix() ? ' ' . $this->getTitleSeparator() . ' ' : '')
			. $this->getTitleSuffix();

		return trim($preparedTitle);

	}


	protected function getTitleSuffix()
	{
		return $this->scopeConfig->getValue(
			'design/head/title_suffix',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}

	protected function getTitlePrefix()
	{
		return $this->scopeConfig->getValue(
			'design/head/title_prefix',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}

	protected function getTitleSeparator()
	{
		$titleSeparatorValue = $this->scopeConfig->getValue(
			'seo/general/title_separator',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
		if ($titleSeparatorValue == null)
			return null;

		return $this->_titleSeparator->toArray()[$titleSeparatorValue];
	}
}