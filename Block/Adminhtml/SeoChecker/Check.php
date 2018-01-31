<?php
/**
 * Created by PhpStorm.
 * User: nghia
 * Date: 26/01/2018
 * Time: 08:51
 */

namespace Mageplaza\Seo\Block\Adminhtml\SeoChecker;

use Magento\Framework\View\Element\Template;

class Check extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'seocheck.phtml';


    public $url;
    public $cmsUrl;
    public $cmsPageFactory;
    public $sitemapCollection;
    public $jsonHelper;


    public function __construct(
        \Magento\Framework\Url $url,
        \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder $cmsUrl,
        \Magento\Cms\Model\PageFactory $cmsPageFactory,
        \Magento\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory $sitemapCollection,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Template\Context $context,
        array $data = []
    )
    {
        $this->url = $url;
        $this->cmsUrl = $cmsUrl;
        $this->cmsPageFactory = $cmsPageFactory;
        $this->sitemapCollection = $sitemapCollection;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    public function getLink()
    {
        $id = $this->_request->getParam('id');
        $storeCode = $this->_storeManager->getStore()->getCode();
        $actionName = $this->_request->getFullActionName();
        switch ($actionName) {
            case 'catalog_product_edit':
                return $this->url->getUrl('catalog/product/view', ['id' => $id, '_nosid' => true, '_query' => ['___store' => $storeCode]]);
            case 'catalog_category_edit':
                return $this->url->getUrl('catalog/category/view', ['id' => $id, '_nosid' => true, '_query' => ['___store' => $storeCode]]);
            case 'cms_page_edit':
                $pageId = $this->_request->getParam('page_id');
                $storeId = $this->_storeManager->getStore()->getId();
                return $this->cmsUrl->getUrl($this->cmsPageFactory->create()->load($pageId)->getIdentifier(), $storeId, $storeCode);
            default:
                return '';
        }
    }

    public function sitemap()
    {
        $sitemapLinks=[];
        if ($this->_storeManager->getStore()->getId() == '0') {
            $sitemap = $this->sitemapCollection->create();
        } else {
            $sitemap = $this->sitemapCollection->create()->addStoreFilter([$this->_storeManager->getStore()->getId()]);
        }
        foreach ($sitemap as $item){
             $sitemapLinks[]=$this->getBaseUrl().ltrim($item->getSitemapPath(),'/').$item->getSitemapFilename();
        }
        return $sitemapLinks;
    }

    public function getSeoData()
    {
        $data=[];
        $data['link']=$this->getLink();
        $data['sitemap']=$this->sitemap();
        $data['baseUrl']=$this->getBaseUrl();
        return $this->jsonHelper->jsonEncode($data);
    }

}