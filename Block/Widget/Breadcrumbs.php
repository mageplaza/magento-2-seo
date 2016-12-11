<?php
namespace Mageplaza\Seo\Block\Widget;

use Mageplaza\Seo\Helper\Data as HelperData;
use Magento\Backend\Block\Widget\Breadcrumbs as WidgetBreadcrumbs;

class Breadcrumbs extends WidgetBreadcrumbs
{
	/**
	 * @var string
	 */
	protected $_template = 'Mageplaza_Seo::richsnippets/jsonld/breadcrumb.phtml';

	private $_helperData;

	/**
	 * @return void
	 */
	protected function _construct(
		HelperData $helperData
	)
	{
		$this->_helperData = $helperData;

		return parent::_construct();
	}


	/**
	 * get seo helper
	 *
	 * @return \Mageplaza\Seo\Helper\Data
	 */
	public function getHelper()
	{
		return $this->_helperData;
	}

}