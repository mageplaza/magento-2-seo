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
    protected $categoryRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        ObjectManagerInterface $objectManager,
        \Magento\Theme\Block\Html\Topmenu $topMenu,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $collection,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository
    ) {
        $this->objectManager       = $objectManager;
        $this->collection          = $collection;
        $this->_categoryHelper     = $categoryHelper;
        $this->_categoryCollection = $categoryCollection;
        $this->categoryRepository  = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Catalog\Helper\Category
     */
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }

    /**
     * get product collection
     *
     * @return mixed
     */
    public function getProductCollection()
    {

        $collection = $this->objectManager->create('Magento\Catalog\Model\Product')->getCollection()
            ->addAttributeToFilter('status', 1);

        return $collection;
    }

    public function getCategoryCollection()
    {

        $collection = $this->objectManager->create('Magento\Catalog\Model\Category')->getCollection()
            ->addAttributeToFilter('status', 1);

        return $collection;
    }

    /**
     * get category url
     *
     * @param $category
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryUrl($category)
    {
        $cat = $this->categoryRepository->get($category->getId());

        return $this->_categoryHelper->getCategoryUrl($cat);
    }

}