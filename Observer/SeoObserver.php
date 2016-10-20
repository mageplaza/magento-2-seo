<?php

namespace Mageplaza\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Filesystem\DirectoryList;
use Mageplaza\Seo\Helper\Data as SeoHelper;

class SeoObserver extends \Magento\Framework\App\Config\Value implements ObserverInterface
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

    /**
     * SeoObserver constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $config
     * @param \Magento\Framework\App\Cache\TypeListInterface               $cacheTypeList
     * @param \Magento\Framework\Filesystem                                $filesystem
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param \Mageplaza\Seo\Helper\Data                                   $helper
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        SeoHelper $helper,
        array $data = []
    ) {
        $this->_directory    = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_fileRobot    = 'robots.txt';
        $this->_fileHtaccess = '.htaccess';
        $this->_helper       = $helper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);

    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        $value = $this->_helper->getRobotsConfig('content');
        $this->_directory->writeFile($this->_fileRobot, $value);
        $value = $this->_helper->getHtaccessConfig('content');
        $this->_directory->writeFile($this->_fileHtaccess, $value);
    }


}