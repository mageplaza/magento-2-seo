<?php

namespace Mageplaza\Seo\Block\Page\Head;

use Mageplaza\Seo\Block\AbstractSeo;

class Verification extends AbstractSeo
{

	/**
	 * get Google verification code
	 * @return mixed
	 */
	public function getGoogleVerificationCode()
	{
		return $this->getHelper()->getVerficationConfig('google');

	}

	/**
	 * get Bing verification code
	 * @return mixed
	 */
	public function getBingVerificationCode()
	{
		return $this->getHelper()->getVerficationConfig('bing');

	}

	/**
	 * get Pinterest verification code
	 * @return mixed
	 */
	public function getPinterestVerificationCode()
	{
		return $this->getHelper()->getVerficationConfig('pinterest');

	}

	/**
	 * get Yandex verification code
	 * @return mixed
	 */
	public function getYandexVerificationCode()
	{
		return $this->getHelper()->getVerficationConfig('yandex');

	}







}