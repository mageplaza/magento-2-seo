<?php

namespace Mageplaza\Seo\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\ObjectManagerInterface;

class Sitemap extends Template
{
    protected $objectManager;
    protected $_categoryHelper;
    protected $categoryFlatConfig;
    protected $topMenu;
    protected $_categoryCollection;
    protected $collection;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
                                \Magento\Catalog\Helper\Category $categoryHelper,
                                \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
                                ObjectManagerInterface $objectManager,
                                \Magento\Theme\Block\Html\Topmenu $topMenu,
                                \Magento\Catalog\Model\ResourceModel\Category\Collection $collection,
                                \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection)
    {
        $this->objectManager = $objectManager;
        $this->collection=$collection;
        $this->_categoryHelper = $categoryHelper;
        $this->_categoryCollection = $categoryCollection;
        parent::__construct($context);
    }
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }
    public function getProductCollection(){
       return $this->getCoreObject('Magento\Catalog\Model\Category')
           ->getProductCollection()
           ->addAttributeToSelect('*');
    }
    public function getCategoryCollection()
    {
//        $collection = $this->_categoryCollection->create()
//            ->addAttributeToSelect('*');
        $collection = $this->collection
            ->addAttributeToSelect('*');
        return $collection;
    }
    public function getCoreObject($helper)
    {
        return $this->objectManager->create($helper);
    }
}