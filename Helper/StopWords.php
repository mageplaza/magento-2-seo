<?php

namespace Mageplaza\Seo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Mageplaza\Seo\Model\Source\Language;
use Magento\Framework\App\ObjectManager;

class StopWords
{

	protected $_storeManagerInterface;

	protected $_lang;

	public function __construct(
		\Mageplaza\Seo\Model\Source\Language $language,
		\Magento\Store\Model\StoreManagerInterface $storeManagerInterface
	)
	{
		$this->_lang = $language;
		$this->_storeManagerInterface = $storeManagerInterface;
	}

	/**
	 * get stop words
	 * @return array
	 */
	public function _getStopWords($storeId)
	{
		if($storeId == 0 )
			return [];
		$packetLang = $this->_storeManagerInterface->getStore($storeId)->getMpLang();
		switch ($packetLang){
			case Language::DE:
				return ObjectManager::getInstance()->create('\Mageplaza\Seo\Helper\StopWords\De')->getStopWords();
			case Language::EN:
				return ObjectManager::getInstance()->create('\Mageplaza\Seo\Helper\StopWords\En')->getStopWords();
			case Language::ES:
				return ObjectManager::getInstance()->create('\Mageplaza\Seo\Helper\StopWords\Es')->getStopWords();
			case Language::FR:
				return ObjectManager::getInstance()->create('\Mageplaza\Seo\Helper\StopWords\Fr')->getStopWords();
			case Language::OTHER:
				return [];

		}
	}

	public function filterStopWords($url,$storeId)
	{

		$stopWords = $this->_getStopWords($storeId);
		$arrWordUrl = $this->convertUrlToArray($url);
		foreach ($arrWordUrl as $index => $item)
			if(in_array($item,$stopWords))
			{
				unset($arrWordUrl[$index]);
			}
		return $this->convertArrayToUrl($arrWordUrl);
	}

	public function convertUrlToArray($url)
	{
		return explode('-',$url);
	}

	public function convertArrayToUrl($arr)
	{
		$url ='';
		foreach ($arr as $item){
			$url = $url.'-'.$item;
		}
		return substr($url,1);
	}

}