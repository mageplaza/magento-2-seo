<?php

namespace Mageplaza\Seo\Block\Page\Head;

use Mageplaza\Seo\Block\AbstractSeo;

class Product extends AbstractSeo
{

	protected $imageHelper;



	public function getProductImage()
	{

		$imageUrl = $this->objectManager->get('Magento\Catalog\Helper\Image')
			->init($this->getProduct(), 'product_base_image')
			->getUrl();

		return $imageUrl;
	}

}