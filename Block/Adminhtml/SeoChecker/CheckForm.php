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
use Magento\Cms\Helper\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Seo\Helper\Data as SeoHelperData;

/**
 * Class CheckForm
 * @package Mageplaza\Seo\Block\Adminhtml\SeoChecker
 */
class CheckForm extends Template
{
    const SEO_TOOL_URL = 'https://pagespeed.web.dev/report';

    /**
     * @var string
     */
    protected $_template = 'seocheck.phtml';

    /**
     * @var Page
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
     * @param Context $context
     * @param UrlBuilder $cmsUrl
     * @param PageFactory $cmsPageFactory
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SeoHelperData $helper
     * @param array $data
     */
    public function __construct(
        Context                     $context,
        Page                        $cmsUrl,
        PageFactory                 $cmsPageFactory,
        ProductFactory              $productFactory,
        ProductRepositoryInterface  $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        SeoHelperData               $helper,
        array                       $data = []
    ) {
        $this->helper             = $helper;
        $this->productFactory     = $productFactory;
        $this->categoryRepository = $categoryRepository;
        $this->cmsUrl             = $cmsUrl;
        $this->cmsPageFactory     = $cmsPageFactory;
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
        $storeId    = $this->_request->getParam('store');
        $actionName = $this->_request->getFullActionName();
        if ($storeId === 0 || $storeId === null) {
            $defaultStore = $this->_storeManager->getDefaultStoreView();
            $storeId      = $defaultStore->getId();
        }
        switch ($actionName) {
            case 'catalog_product_edit':
                $urlModel = $this->productRepository->getById($id)->getUrlModel();
                $product  = $this->productFactory->create()->load($id)->setStoreId($storeId);
                $url      = $urlModel->getUrl($product);
                break;
            case 'catalog_category_edit':
                $category = $this->categoryRepository->get($id, $storeId);
                $url      = $category->getUrl();
                break;
            case 'cms_page_edit':
                $pageId = $this->_request->getParam('page_id');
                $url    = $this->cmsUrl->getPageUrl($pageId);
                break;
            default:
                $url = '';
        }

        return $url;
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
