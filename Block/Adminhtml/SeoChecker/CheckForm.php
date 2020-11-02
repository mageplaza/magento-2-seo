<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Block\Adminhtml\SeoChecker;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magento\Framework\Url;
use Magento\Framework\View\Element\Template;
use Magento\Sitemap\Model\ResourceModel\Sitemap\Collection;
use Magento\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory;
use Mageplaza\Seo\Helper\Data as SeoHelperData;

/**
 * Class CheckForm
 * @package Mageplaza\Seo\Block\Adminhtml\SeoChecker
 */
class CheckForm extends Template
{
    const SEO_TOOL_URL = 'http://seo.mageplaza.com';

    /**
     * @var string
     */
    protected $_template = 'seocheck.phtml';

    /**
     * @var UrlBuilder
     */
    protected $cmsUrl;

    /**
     * @var PageFactory
     */
    protected $cmsPageFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var CollectionFactory
     */
    protected $sitemapCollection;

    /**
     * @var JsonData
     */
    protected $jsonHelper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var SeoHelperData
     */
    protected $helper;

    /**
     * CheckForm constructor.
     *
     * @param Template\Context $context
     * @param Url $url
     * @param UrlBuilder $cmsUrl
     * @param PageFactory $cmsPageFactory
     * @param CollectionFactory $sitemapCollection
     * @param JsonData $jsonHelper
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SeoHelperData $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Url $url,
        UrlBuilder $cmsUrl,
        PageFactory $cmsPageFactory,
        CollectionFactory $sitemapCollection,
        JsonData $jsonHelper,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        SeoHelperData $helper,
        array $data = []
    ) {
        $this->helper             = $helper;
        $this->productFactory     = $productFactory;
        $this->categoryRepository = $categoryRepository;
        $this->cmsUrl             = $cmsUrl;
        $this->cmsPageFactory     = $cmsPageFactory;
        $this->sitemapCollection  = $sitemapCollection;
        $this->jsonHelper         = $jsonHelper;
        $this->productRepository  = $productRepository;

        parent::__construct($context, $data);
    }

    /**
     * get link to check
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getLink()
    {
        $id         = $this->_request->getParam('id');
        $storeCode  = $this->_storeManager->getStore()->getCode();
        $storeId    = $this->_storeManager->getStore()->getId();
        $actionName = $this->_request->getFullActionName();
        if ($storeId === 0) {
            $defaultStore = $this->_storeManager->getDefaultStoreView();
            $storeId      = $defaultStore->getId();
            $storeCode    = $defaultStore->getCode();
        }

        switch ($actionName) {
            case 'catalog_product_edit':
                $urlModel = $this->productRepository->getById($id)->getUrlModel();
                $product  = $this->productFactory->create()->load($id)->setStoreId($storeId);
                $url      = $urlModel->getUrl($product, ['_query' => ['___store' => $storeCode]]);
                break;
            case 'catalog_category_edit':
                $category = $this->categoryRepository->get($id, $storeId);
                $url      = $category->getUrl(['_query' => ['___store' => $storeCode]]);
                break;
            case 'cms_page_edit':
                $pageId = $this->_request->getParam('page_id');
                $url    = $this->cmsUrl->getUrl(
                    $this->cmsPageFactory->create()->load($pageId)->getIdentifier(),
                    $storeId,
                    $storeCode
                );
                break;
            default:
                $url = '';
        }

        return $url;
    }

    /**
     * get site map links
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function sitemap()
    {
        $sitemapLinks = [];

        /** @var Collection $sitemap */
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
     * @throws NoSuchEntityException
     */
    public function getSeoData()
    {
        $data            = [];
        $data['link']    = $this->getLink();
        $data['sitemap'] = $this->sitemap();
        $data['baseUrl'] = $this->getBaseUrl();

        return SeoHelperData::jsonEncode($data);
    }

    /**
     * get Seo Tool Url
     *
     * @return string
     */
    public function getSeoToolUrl()
    {
        return self::SEO_TOOL_URL;
    }
}
