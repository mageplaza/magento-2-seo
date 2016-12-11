<?php

namespace Mageplaza\Seo\Block\Navigation;

use Mageplaza\Seo\Block\Navigation\AbstractNavigation as Navigation;

class Category extends Navigation
{

	public function getPager()
	{
		return $this->getLayout()->getBlock('product_list_toolbar_pager');
	}


	/**
	 * get next page url
	 * @return string
	 */
	public function getNextPageUrl()
	{
		$pager = $this->getPager();

		//if has 1 page
		if($pager->getTotalNum()  <= 1) return null;

		if(!$pager->isLastPage()){
			return $pager->getNextPageUrl();
		}
		return null;
	}



	/**
	 * get prev page url
	 * @return string
	 */
	public function getPreviousPageUrl()
	{
		$pager = $this->getPager();

		//if has 1 page
		if($pager->getTotalNum()  <= 1) return null;

		if(!$pager->isFirstPage()){
			return $pager->getPreviousPageUrl();
		}
		return null;
	}

}