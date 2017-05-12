<?php

namespace Mageplaza\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Filesystem\DirectoryList;
use Mageplaza\Seo\Helper\Data as SeoHelper;
use Mageplaza\Seo\Model\Source\Language;
use Mageplaza\Seo\Plugin\Title;

class Product extends \Magento\Framework\App\Config\Value implements ObserverInterface
{
	/**
	 * @var \Magento\Framework\Filesystem\Directory\Write
	 */
	protected $_directory;

	/**
	 * @var string
	 */
	protected $_fileRobot;
	/**
	 * @var string
	 */
	protected $_fileHtaccess;
	/**
	 * @var string
	 */
	protected $_helper;

	protected $_language;

	protected $_stopWord;

	protected $_productFactory;

	/**
	 * SeoObserver constructor.
	 *
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
	 * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
	 * @param \Magento\Framework\Filesystem $filesystem
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
	 * @param \Mageplaza\Seo\Helper\Data $helper
	 * @param array $data
	 */
	public function __construct(
		\Mageplaza\Seo\Helper\StopWords $stopWords,
		\Mageplaza\Seo\Model\Source\Language $language,
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\App\Config\ScopeConfigInterface $config,
		\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		SeoHelper $helper,
		array $data = []
	)
	{
		$this->_stopWord       = $stopWords;
		$this->_language       = $language;
		$this->_directory      = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
		$this->_fileRobot      = 'robots.txt';
		$this->_fileHtaccess   = '.htaccess';
		$this->_helper         = $helper;
		$this->_productFactory = $productFactory;
		parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
	}

	/**
	 * @param \Magento\Framework\Event\Observer $observer
	 */
	public function execute(Observer $observer)
	{
		/** @type \Magento\Catalog\Model\Product $product */
		$product = $observer->getProduct();
		$product->setUrlKey($this->_stopWord->filterStopWords($product->getUrlKey(), $product->getStoreId()));
		$product->save();
	}
}