<?php

namespace Mageplaza\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Mageplaza\Seo\Helper\Data as SeoHelper;
use Magento\Framework\View\Page\Config;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Context;
use Magento\Framework\App\Request\Http as Url;
use Magento\Store\Model\Group;

class GenerateBlocksAfterObserver implements ObserverInterface
{
    protected $helper;
    protected $pageConfig;
    protected $registry;
    protected $objectManager;
    protected $urlManager;
    protected $context;
    protected $url;
    protected $storeGroup;

    public function __construct(
    	SeoHelper $helper,
		Config $pageConfig,
        Registry $registry,
		ObjectManagerInterface $objectManager,
        UrlInterface $urlManager,
		Context $context,
		Url $url,
		Group $storeGroup
    ) {
        $this->helper        = $helper;
        $this->pageConfig    = $pageConfig;
        $this->registry      = $registry;
        $this->objectManager = $objectManager;
        $this->urlManager    = $urlManager;
        $this->context       = $context;
        $this->url           = $url;
        $this->storeGroup    = $storeGroup;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $this->basicSetup($observer);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->objectManager->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getStore()->getBaseUrl();
    }

    /**
     * @param $observer
     */
    public function basicSetup($observer)
    {
        /**
         * @var \Magento\Framework\View\LayoutInterface
         */
        $layout = $observer->getEvent()->getLayout();
        $action = $observer->getEvent()->getFullActionName();
        /**
         * catalog_category_view
         * vendor/magento/module-catalog/Block/Category/View.php:72
         */
        if ($action == 'catalog_category_view') {
            $category        = $this->registry->registry('current_category');
            $pageRobots      = $category->getMpMetaRobots();
        }

        /**
         * catalog_product_view
         * vendor/magento/module-catalog/Block/Product/View.php:135
         */
        if ($action == 'catalog_product_view') {
            $product   = $this->registry->registry('current_product');
            $pageRobots   = $product->getMpMetaRobots();
        }

        /**
         * home page cms page
         */
        if ($action == 'cms_index_index' OR $action == 'cms_page_view') {
            $page            = $this->objectManager->get(
                'Magento\Cms\Model\Page'
            );
            if ($action == 'cms_index_index') {
                $url = $this->urlManager->getBaseUrl();
            } else {
                $url = $this->urlManager->getUrl($page->getIdentifier());
            }
        }

        if ( ! empty($pageRobots)) {
            $this->pageConfig->setRobots($pageRobots);
        }



    }

    /**
     * @return mixed
     */
    public function getLangCode()
    {
        $code = $this->storeGroup->getDefaultStore()->getLocaleCode();
        $code = strtolower($code);

        return $code;
    }
}