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

		if ($headBlock != false) {

			$afterBodyStartContainer = $this->renderContainer('after.body.start', $layout);
			$afterBodyStartContainer = str_replace([' ', "\n"], ['', ''], $afterBodyStartContainer);

			/**
			 * Add rich snippets organization
			 */
			$subString = '<scripttype="application/ld+json">{"@context":"http://schema.org","@type":"Organization"';
			if (!$afterBodyStartContainer || strpos($afterBodyStartContainer, $subString) === false) {
				$layout->addBlock('\Mageplaza\Seo\Block\Richsnippets\Organization', 'mageplaza_seo_organization', 'after.body.start', '');
			}

			/**
			 * Add rich snippets sitename, sitelinks
			 */
			$subString = '<scripttype="application/ld+json">{"@context":"http://schema.org","@type":"WebSite"';
			if (!$afterBodyStartContainer || strpos($afterBodyStartContainer, $subString) === false) {
				$layout->addBlock('\Mageplaza\Seo\Block\Sitelinks', 'mageplaza_seo_sitelinks', 'after.body.start', '');
			}

			/**
			 * Add markup data to specify action
			 */
			switch ($action) {
				case 'catalog_category_view':
					$headBlock->addChild(
						'mageplaza_seo_open_graph',
						'\Mageplaza\Seo\Block\Page\Head\Category',
						['template' => 'opengraph/category.phtml']
					);
					break;
				case 'catalog_category_view_type_default':
					$headBlock->addChild(
						'mageplaza_seo_open_graph',
						'\Mageplaza\Seo\Block\Page\Head\Category',
						['template' => 'opengraph/category.phtml']
					);
					break;
				case 'cms_page_view':
					$headBlock->addChild(
						'mageplaza_seo_open_graph',
						'\Mageplaza\Seo\Block\Page\Head\Page',
						['template' => 'opengraph/cms.phtml']
					);
					break;
				case 'catalog_product_view':
					$headBlock->addChild(
						'mageplaza_seo_open_graph',
						'\Mageplaza\Seo\Block\Page\Head\Product',
						['template' => 'opengraph/product.phtml']
					);
					/**
					 * Add rich snippet product
					 */

					$layout->addBlock(
						'\Mageplaza\Seo\Block\Richsnippets\Product',
						'mageplaza_seo_richsnippets_product',
						'after.body.start', '');

//					$subString = '<script type="application/ld+json">{"@context":"http://schema.org/","@type":"Product"';
//					if (!$afterBodyStartContainer || strpos($afterBodyStartContainer, $subString) === false) {
//					}

					break;
			}
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

	/**
	 * Get block by name
	 *
	 * @param string $name
	 * @param \Magento\Framework\View\LayoutInterface $layout
	 * @return string
	 */
	public function renderContainer($name, $layout)
	{
		$html     = '';
		$children = $layout->getChildNames($name);
		foreach ($children as $child) {
			$html .= $layout->renderElement($child);
		}

		return $html;
	}

	protected $logger;

	public function __construct(\Psr\Log\LoggerInterface $logger)
	{
		$this->logger = $logger;
	}
}