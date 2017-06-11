<?php
namespace Mageplaza\Seo\Block\Widget;

use Magento\Theme\Block\Html\Breadcrumbs as HtmlBreadcrumbs;

class Breadcrumbs extends HtmlBreadcrumbs
{
	/**
	 * @var string
	 */
	protected $_template = 'Mageplaza_Seo::richsnippets/jsonld/breadcrumb.phtml';


	/**
	 * @return array
	 */
	public function getBreadcrumbLinks(){
		return $this->_crumbs;
	}
}