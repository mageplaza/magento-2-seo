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
    protected $_helper;

    /**
     * Sitemap constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context                $context
     * @param \Magento\Catalog\Helper\Category                                $categoryHelper
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State              $categoryFlatState
     * @param \Magento\Framework\ObjectManagerInterface                       $objectManager
     * @param \Magento\Theme\Block\Html\Topmenu                               $topMenu
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection        $collection
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
     * @param \Magento\Catalog\Model\CategoryRepository                       $categoryRepository
     * @param \Mageplaza\Seo\Helper\Data                                      $helper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        ObjectManagerInterface $objectManager,
        \Magento\Theme\Block\Html\Topmenu $topMenu,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $collection,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Mageplaza\Seo\Helper\Data $helper
    ) {
        $this->objectManager       = $objectManager;
        $this->collection          = $collection;
        $this->_categoryHelper     = $categoryHelper;
        $this->_categoryCollection = $categoryCollection;
        $this->categoryRepository  = $categoryRepository;
        $this->_helper             = $helper;

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
     * get seo helper
     *
     * @return \Mageplaza\Seo\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * get sitemap config
     *
     * @param $code
     *
     * @return mixed
     */
    public function getSitemapConfig($code)
    {
        return $this->_helper->getHtmlsitemapConfig($code);

    }

    /**
     * get product collection
     *
     * @return mixed
     */
    public function getProductCollection()
    {

        $limit = $this->getSitemapConfig('product_limit') ? $this->getSitemapConfig('product_limit') : 100;

        $visibleProducts = $this->objectManager->create('\Magento\Catalog\Model\Product\Visibility')
            ->getVisibleInCatalogIds();
        $collection      = $this->objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->setVisibility($visibleProducts);
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->setPageSize($limit)
            ->addAttributeToSelect('*');


        return $collection;
    }

    public function getCategoryCollection()
    {

        $collection = $this->objectManager->create('Magento\Catalog\Model\ResourceModel\Category\Collection')
            ->addAttributeToFilter(
                'entity_id', [
                    'nin' => [1, 2]
                ]
            )
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSelect('*');

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

    /**
     * get page collection
     * @return mixed
     */
    public function getPageCollection()
    {
        $collection = $this->objectManager->create('\Magento\Cms\Model\ResourceModel\Page\Collection');
        $collection->addFieldToFilter('is_active', \Magento\Cms\Model\Page::STATUS_ENABLED);
        $pages = [];

        return $collection;
    }

    /**
     * get excluded pages
     * @return array
     */
    public function getExcludedPages()
    {
        return [
            'home',
            'no-route',
        ];
    }
}