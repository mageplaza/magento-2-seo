<?php

namespace Mageplaza\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Mageplaza\Seo\Helper\Data as SeoHelper;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
class GenerateBlocksAfterObserver implements ObserverInterface
{
    protected $helper;
    protected $registry;
    protected $objectManager;
    protected $urlManager;

    public function __construct(
        SeoHelper $helper,
        Registry $registry,
        ObjectManagerInterface $objectManager,
        UrlInterface $urlManager
    )
    {
        $this->helper = $helper;
        $this->registry = $registry;
        $this->objectManager=$objectManager;
        $this->urlManager=$urlManager;
    }

    public function execute(Observer $observer)
    {
        $this->basicSetup($observer);

        return $this;
    }
    public function getBaseUrl()
    {
        return $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()
            ->getBaseUrl();
    }

    public function basicSetup($observer)
    {
        $action = $this->getActionName($observer);
        $layout = $observer->getEvent()->getLayout();
        /**
         * catalog_category_view
         */
        if ($action == 'catalog_category_view') {
            $category        = $this->registry->registry('current_category');
            $pageTitle       = $category->getName();
            $pageDescription = $category->getMetaDescription();
            $pageKeywords    = $category->getMetaKeywords();
            $pageRobots      = $category->getMetaRobots();
            $url             = $category->getUrl();

        }

        /**
         * catalog_product_view
         */
        if ($action == 'catalog_product_view') {
            $product         = $this->registry->registry('current_product');
            $pageTitle       = $product->getName();

            /**
             * Auto set page title, meta description
             */
            if(empty($product->getMetaDescription())){
                $pageDescription = trim(strip_tags($product->getShortDescription()));
            } else{
                $pageDescription = trim(strip_tags($product->getMetaDescription()));
            }
            $pageKeywords    = $product->getMetaKeywords();
            $pageRobots      = $product->getMetaRobots();
            $url             = $product->getUrl();
        }
        if ($action == 'cms_index_index' OR $action == 'cms_page_view') {
            $page = $this->registry->registry('cms/page');
            $pageTitle       = $page->getTitle();
            $pageDescription = $page->getMetaDescription();
            $pageKeywords    = $page->getMetaKeywords();
            $pageRobots      = $page->getMetaRobots();
            if ($action == 'cms_index_index') {
                $url = $this->urlManager->getBaseUrl();
            } else {
                $url = $this->urlManager->getUrl($page->getIdentifier());
            }
        }

        if ($head = $layout->getBlock('head')) {
            if (!empty($pageTitle)) $head->setTitle($pageTitle);
            if (!empty($pageDescription)) $head->setDescription($pageDescription);
            if (!empty($pageKeywords)) $head->setMetaKeywords($pageKeywords);
            if (!empty($pageRobots)) $head->setRobots($pageRobots);
            if (!empty($url)) $head->addLinkRel('canonical', $url);
            if (!empty($url)) $head->addItem('link_rel', $url, 'rel="alternate" hreflang="' . $this->getLangCode() . '"');
        }

        $layout->generateXml();

    }
}