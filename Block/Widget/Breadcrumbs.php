<?php
namespace Mageplaza\Seo\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Breadcrumbs as HtmlBreadcrumbs;
use Magento\Framework\Registry;
use Magento\Framework\App\ObjectManager;

/**
 * Class Breadcrumbs
 * @package Mageplaza\Seo\Block\Widget
 */
class Breadcrumbs extends HtmlBreadcrumbs
{
	/**
	 * @var string
	 */
	protected $_template = 'Mageplaza_Seo::richsnippets/jsonld/breadcrumb.phtml';

	/**
	 * @type Registry
	 */
	protected $_coreRegistry;

	/**
	 * @type \Magento\Framework\App\ObjectManager
	 */
	protected $_objectManager;

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
		$this->_objectManager=ObjectManager::getInstance();
		$this->_coreRegistry = $this->_objectManager->get('Magento\Framework\Registry');
		parent::__construct($context, $data);
	}

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$this->_crumbs = $this->_coreRegistry->registry('crumbs');

		if (is_array($this->_crumbs)) {
			reset($this->_crumbs);
			$this->_crumbs[key($this->_crumbs)]['first'] = true;
			end($this->_crumbs);
			$this->_crumbs[key($this->_crumbs)]['last'] = true;
		}
		$this->assign('crumbs', $this->_crumbs);

		return parent::_toHtml();
	}
}