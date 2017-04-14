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

		$blocks = $layout->getAllBlocks();
		/* @var \Magento\Framework\View\Element\AbstractBlock $block */
		foreach ($blocks as $name => $block) {
			if ($name == 'head.additional' && (strpos($block->toHtml(), 'rel="canonical"') === false)) {
				$block->addChild(
					'mageplaza_seo_canonical',
					'\Mageplaza\Seo\Block\Page\Head\Page',
					['template' => 'page/head/canonical.phtml']
				);
			}
		}
	}


}