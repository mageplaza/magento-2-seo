<?php

namespace Mageplaza\Seo\Block\Page\Head\Search;

use Magento\Framework\View\Element\Template;

class Catalog extends Template
{
	/**
	 * @var \Magento\Framework\View\Page\Config $_corePageConfig ;
	 */
	protected $_corePageConfig;

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param array $data
	 */
	public function __construct(
		Template\Context $context,
		array $data = []
	)
	{
		$this->_corePageConfig = $context->getPageConfig();
		parent::__construct($context, $data);
	}

	/**
	 * Prepare layout
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		$this->_corePageConfig->setRobots('NOINDEX,NOFOLLOW');

		return parent::_prepareLayout();
	}
}