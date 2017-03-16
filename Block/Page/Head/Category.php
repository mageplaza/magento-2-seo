<?php

namespace Mageplaza\Seo\Block\Page\Head;

use Mageplaza\Seo\Block\AbstractSeo;


class Category extends AbstractSeo
{
	public function getDefaultContent()
	{
		return null;
	}

	public function entityEnable()
	{
		return $this->hreflang->hasEnableForEntity('enable_category');
	}
}