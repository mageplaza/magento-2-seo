<?php

namespace Mageplaza\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class Markup implements ObserverInterface
{
	/**
	 * @param \Magento\Framework\Event\Observer $observer
	 */
	public function execute(Observer $observer)
	{
		/* @var \Magento\Framework\View\LayoutInterface $layout */
		$layout = $observer->getEvent()->getLayout();
		$action = $observer->getEvent()->getFullActionName();

		/**
		 * Add Canonical tag
		 */
		$headBlock = $this->getBlock('head.additional', $layout);
		if (strpos($headBlock->toHtml(), 'rel="canonical"') === false) {
			$headBlock->addChild(
				'mageplaza_seo_canonical',
				'\Mageplaza\Seo\Block\Page\Head\Page',
				['template' => 'page/head/canonical.phtml']
			);
		}

		/**
		 * Add markup data to specify action
		 */
		switch($action){
			case 'catalog_category_view':
				if (strpos($headBlock->toHtml(), 'hrefLang') === false) {
					$headBlock->addChild(
						'mageplaza_seo_open_graph',
						'\Mageplaza\Seo\Block\Page\Head\Category',
						['template' => 'opengraph/category.phtml']
					);
				}
				break;
			case 'catalog_category_view_type_default':
				if (strpos($headBlock->toHtml(), 'hrefLang') === false) {
					$headBlock->addChild(
						'mageplaza_seo_open_graph',
						'\Mageplaza\Seo\Block\Page\Head\Category',
						['template' => 'opengraph/category.phtml']
					);
				}
				break;
			case 'cms_page_view':
				if (strpos($headBlock->toHtml(), 'hrefLang') === false) {
					$headBlock->addChild(
						'mageplaza_seo_open_graph',
						'\Mageplaza\Seo\Block\Page\Head\Page',
						['template' => 'opengraph/cms.phtml']
					);
				}
				break;
			case 'catalog_product_view':
				if (strpos($headBlock->toHtml(), 'hrefLang') === false) {
					$headBlock->addChild(
						'mageplaza_seo_open_graph',
						'\Mageplaza\Seo\Block\Page\Head\Product',
						['template' => 'opengraph/product.phtml']
					);
				}
				break;
		}


	}

	/**
	 * Get block by name
	 *
	 * @param string $name
	 * @param \Magento\Framework\View\LayoutInterface $layout
	 * @return \Magento\Framework\View\Element\AbstractBlock|bool
	 */
	public function getBlock($name, $layout)
	{
		$blocks = $layout->getAllBlocks();

		return isset($blocks[$name]) ? $blocks[$name] : false;
	}
}