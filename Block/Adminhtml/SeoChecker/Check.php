<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Seo
 * @copyright   Copyright (c) 2018 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Block\Adminhtml\SeoChecker;

use Magento\Framework\View\Element\Template;
use Mageplaza\Seo\Helper\Data;

/**
 * Class Check
 * @package Mageplaza\Seo\Block\Adminhtml\SeoChecker
 */
class Check extends Template
{
    /**
     * @var string
     */
    protected $_template = 'seocheck.phtml';

    /**
     * @var \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder
     */
    protected $cmsUrl;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $cmsPageFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory 
     */
    protected $productFactory;

    /**
     * @var \Magento\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory
     */
    protected $sitemapCollection;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Mageplaza\Seo\Helper\Data
     */
    protected $helper;

    /**
     * Check constructor.
     * @param Data $helper
     * @param \Magento\Framework\Url $url
     * @param \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder $cmsUrl
     * @param \Magento\Cms\Model\PageFactory $cmsPageFactory
     * @param \Magento\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory $sitemapCollection
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Mageplaza\Seo\Helper\Data $helper,
        \Magento\Framework\Url $url,
        \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder $cmsUrl,
        \Magento\Cms\Model\PageFactory $cmsPageFactory,
        \Magento\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory $sitemapCollection,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        Template\Context $context,
        array $data = []
    )
    {
        $this->helper = $helper;
        $this->productFactory = $productFactory;
        $this->categoryRepository = $categoryRepository;
        $this->cmsUrl = $cmsUrl;
        $this->cmsPageFactory = $cmsPageFactory;
        $this->sitemapCollection = $sitemapCollection;
        $this->jsonHelper = $jsonHelper;
        $this->productRepository = $productRepository;

        parent::__construct($context, $data);
    }

    /**
     * get link to check
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLink()
    {
        $id = $this->_request->getParam('id');
        $storeCode = $this->_storeManager->getStore()->getCode();
        $storeId = $this->_storeManager->getStore()->getId();
        $actionName = $this->_request->getFullActionName();
        if ($storeId == "0") {
            $storeId = $this->_storeManager->getDefaultStoreView()->getId();
            $storeCode = $this->_storeManager->getDefaultStoreView()->getCode();
        }
        switch ($actionName) {
            case 'catalog_product_edit':
                $urlModel = $this->productRepository->getById($id)->getUrlModel();
                $product = $this->productFactory->create()->load($id)->setStoreId($storeId);
                $url = $urlModel->getUrl($product) . "?___store=" . $storeCode;

                return $url;
            case 'catalog_category_edit':
                $category = $this->categoryRepository->get($id, $storeId);
                return $category->getUrl() . "?___store=" . $storeCode;
            case 'cms_page_edit':
                $pageId = $this->_request->getParam('page_id');
                return $this->cmsUrl->getUrl($this->cmsPageFactory->create()->load($pageId)->getIdentifier(), $storeId, $storeCode);
            default:
                return '';
        }
    }

    /**
     * get site map links
     *
     * @return array
     */
    public function sitemap()
    {
        $sitemapLinks = [];

        /** @var \Magento\Sitemap\Model\ResourceModel\Sitemap\Collection $sitemap */
        $sitemap = $this->sitemapCollection->create();

        $storeId = $this->_storeManager->getStore()->getId();
        if ($storeId) {
            $sitemap->addStoreFilter([$storeId]);
        }

        foreach ($sitemap as $item) {
            $sitemapLinks[] = $this->getBaseUrl() . ltrim($item->getSitemapPath(), '/') . $item->getSitemapFilename();
        }
        return $sitemapLinks;
    }

    /**
     * get Data to check
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSeoData()
    {
        $data = [];
        $data['link'] = $this->getLink();
        $data['sitemap'] = $this->sitemap();
        $data['baseUrl'] = $this->getBaseUrl();

        return $this->jsonHelper->jsonEncode($data);
    }

    /**
     * get Seo Tool Url
     *
     * @return string
     */
    public function getSeoToolUrl()
    {
        return $this->helper->getConfigGeneral('seo_tool_url');
    }
}