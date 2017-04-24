<?php

namespace Mageplaza\Seo\Block\Page\Head\Search;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Page\Config as CorePageConfig;

class Catalog extends Template
{
	/**
	 * @var \Magento\Framework\View\Page\Config $_corePageConfig ;
	 */
	protected $_corePageConfig;

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\View\Page\Config $corePageConfig
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param array $data
	 */
	public function __construct(
		CorePageConfig $corePageConfig,
		Template\Context $context,
		array $data = []
	)
	{
		$this->_corePageConfig = $corePageConfig;
		parent::__construct($context, $data);
	}

	/**
	 * Prepare layout
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		$this->_corePageConfig->setRobots('NOINDEX,FOLLOW');

		return parent::_prepareLayout();
	}
}