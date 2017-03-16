<?php

namespace Mageplaza\Seo\Block\Page\Head;

use Mageplaza\Seo\Block\AbstractSeo;

class Dir extends AbstractSeo
{

	/**
	 * get no DMOZ
	 * @return mixed
	 */
	public function getNoDmoz()
	{
		return $this->getHelper()->getGeneralConfig('no_dmoz');
	}

	/**
	 * get no Yahoo dir
	 * @return mixed
	 */
	public function getNoYahooDir()
	{
		return $this->getHelper()->getGeneralConfig('no_yahoo_dir');
	}


}