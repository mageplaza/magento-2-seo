<?php

namespace Mageplaza\Seo\Block;

class Richsnippets extends AbstractSeo
{
	/**
	 * get logo
	 * @return string
	 */
	public function getLogo()
	{
		return $this->logo->getLogoSrc();
	}

	/**
	 * get rich snippet config
	 * @param $code
	 *
	 * @return mixed
	 */
	public function getRichsnippetsHelper($code)
	{
		return $this->helperData->getRichsnippetsConfig($code);
	}

	/**
	 * get profiles
	 * @return mixed
	 */
	public function getProfiles()
	{
		$config   = $this->helperData;
		$profiles = $config->getConfigValue('seo/social_profiles');
		var_dump($profiles);die;
		$lines    = '';
		if ($profiles) {
			$lines = implode(',', $profiles);
		}

		return $lines;

	}
}
