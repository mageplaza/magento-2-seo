<?php
/**
 * * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Seo\Controller\Adminhtml\System\Config\Htaccess;

use Magento\Framework\App\Filesystem\DirectoryList;
use Mageplaza\Seo\Helper\Data as SeoHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Framework\App\Config\ValueFactory as ConfigModel;

class Save extends \Magento\Backend\App\Action
{
    protected $resultJsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory                         $resultJsonFactory
     */
    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $_directory;

    /**
     * @var string
     */
    protected $_fileHtaccess;
    /**
     * @var string
     */
    protected $_helper;

    protected $configModel;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Filesystem $filesystem,
        ConfigModel $configModel,
        SeoHelper $helper

    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_directory        = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_fileHtaccess     = '.htaccess';
        $this->_helper           = $helper;
        $this->configModel       = $configModel;
    }

    public function getContentHtaccesss()
    {
        $data    = $this->getRequest()->getParam('htaccesscontent');
        $content = ($data);

        return $content;
    }

    protected function _saveHtaccess()
    {
        /**
         * DISABLE THIS FEATURE
         */
        
        $value = $this->getContentHtaccesss();
        try {
//            $this->_directory->writeFile($this->_fileHtaccess, $value);
//            $configModel = $this->configModel->create();
//            $configModel = $configModel->getCollection()
//                ->addFieldToFilter('path', 'seo/htaccess/content')
//                ->getFirstItem();
//            $configModel->setValue($value);
//            $configModel->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->_saveHtaccess();
        if ($result) {
            $message = __('Success');
        } else {
            $message = __('Error');
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData(
            [
                'valid'   => (int)$result,
                'message' => $message,
            ]
        );
    }
}
