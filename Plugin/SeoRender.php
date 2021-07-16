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

namespace Mageplaza\Seo\Plugin;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Config\Renderer;
use Magento\Review\Model\Rating;
use Magento\Review\Model\ResourceModel\Review as ReviewResourceModel;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollection;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Seo\Helper\Data as HelperData;
use Mageplaza\Seo\Model\Config\Source\PriceValidUntil;
use Magento\Review\Model\RatingFactory;

/**
 * Class SeoRender
 * @package Mageplaza\Seo\Plugin
 */
class SeoRender
{
    const GOOLE_SITE_VERIFICATION = 'google-site-verification';
    const MSVALIDATE_01           = 'msvalidate.01';
    const P_DOMAIN_VERIFY         = 'p:domain_verify';
    const YANDEX_VERIFICATION     = 'yandex-verification';

    /**
     * @var PageConfig
     */
    protected $pageConfig;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var StockRegistryInterface
     */
    protected $stockState;

    /**
     * @var SearchHelper
     */
    protected $_searchHelper;

    /**
     * @var PriceHelper
     */
    protected $_priceHelper;

    /**
     * @var Manager
     */
    protected $_eventManager;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timeZoneInterface;

    /**
     * @var ReviewCollection
     */
    protected $_reviewCollection;

    /**
     * @var ModuleManager
     */
    protected $_moduleManager;

    /**
     * @var RatingFactory
     */
    protected $ratingFactory;

    /**
     * @var ReviewResourceModel
     */
    protected $reviewResourceModel;

    /**
     * SeoRender constructor.
     *
     * @param PageConfig $pageConfig
     * @param Http $request
     * @param HelperData $helpData
     * @param StockItemRepository $stockItemRepository
     * @param Registry $registry
     * @param ReviewFactory $reviewFactory
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param ProductFactory $productFactory
     * @param ManagerInterface $messageManager
     * @param StockRegistryInterface $stockState
     * @param SearchHelper $searchHelper
     * @param PriceHelper $priceHelper
     * @param Manager $eventManager
     * @param DateTime $dateTime
     * @param TimezoneInterface $timeZoneInterface
     * @param ReviewCollection $reviewCollection
     * @param ModuleManager $moduleManager
     * @param RatingFactory $ratingFactory
     * @param ReviewResourceModel $reviewResourceModel
     */
    public function __construct(
        PageConfig $pageConfig,
        Http $request,
        HelperData $helpData,
        StockItemRepository $stockItemRepository,
        Registry $registry,
        ReviewFactory $reviewFactory,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        ProductFactory $productFactory,
        ManagerInterface $messageManager,
        StockRegistryInterface $stockState,
        SearchHelper $searchHelper,
        PriceHelper $priceHelper,
        Manager $eventManager,
        DateTime $dateTime,
        TimezoneInterface $timeZoneInterface,
        ReviewCollection $reviewCollection,
        ModuleManager $moduleManager,
        RatingFactory $ratingFactory,
        ReviewResourceModel $reviewResourceModel
    ) {
        $this->pageConfig          = $pageConfig;
        $this->request             = $request;
        $this->helperData          = $helpData;
        $this->stockItemRepository = $stockItemRepository;
        $this->registry            = $registry;
        $this->_storeManager       = $storeManager;
        $this->reviewFactory       = $reviewFactory;
        $this->_urlBuilder         = $urlBuilder;
        $this->productFactory      = $productFactory;
        $this->messageManager      = $messageManager;
        $this->stockState          = $stockState;
        $this->_searchHelper       = $searchHelper;
        $this->_priceHelper        = $priceHelper;
        $this->_eventManager       = $eventManager;
        $this->_dateTime           = $dateTime;
        $this->_timeZoneInterface  = $timeZoneInterface;
        $this->_reviewCollection   = $reviewCollection;
        $this->_moduleManager      = $moduleManager;
        $this->ratingFactory       = $ratingFactory;
        $this->reviewResourceModel = $reviewResourceModel;
    }

    /**
     * @param Renderer $subject
     */
    public function beforeRenderMetadata(Renderer $subject)
    {
        if ($this->helperData->isEnabled($this->helperData->getStoreId())) {
            $this->showVerifications();

            $pages = [
                'catalogsearch_result_index',
                'cms_noroute_index',
                'catalogsearch_advanced_result'
            ];
            if (in_array($this->getFullActionName(), $pages)) {
                $this->pageConfig->setMetadata('robots', 'NOINDEX,NOFOLLOW');
            }
        }
    }

    /**
     * @param Renderer $subject
     * @param string $result
     *
     * @return string
     */
    public function afterRenderHeadContent(Renderer $subject, $result)
    {
        if ($this->helperData->isEnabled($this->helperData->getStoreId())) {
            switch ($this->getFullActionName()) {
                case 'catalog_product_view':
                    if ($this->helperData->getRichsnippetsConfig('enable_product')) {
                        $productStructuredData = $this->showProductStructuredData();
                        $result                .= $productStructuredData;
                    }
                    break;
                case 'cms_index_index':
                    if ($this->helperData->getInfoConfig('enable')) {
                        $result .= $this->showBusinessStructuredData();
                    }
                    if ($this->helperData->getRichsnippetsConfig('enable_site_link')) {
                        $result .= $this->showSiteLinksStructuredData();
                    }
                    break;
            }
        }

        return $result;
    }

    /**
     *  Show verifications from config
     */
    public function showVerifications()
    {
        $this->pageConfig->setMetadata(
            self::GOOLE_SITE_VERIFICATION,
            $this->helperData->getVerficationConfig('google')
        );
        $this->pageConfig->setMetadata(
            self::MSVALIDATE_01,
            $this->helperData->getVerficationConfig('bing')
        );
        $this->pageConfig->setMetadata(
            self::P_DOMAIN_VERIFY,
            $this->helperData->getVerficationConfig('pinterest')
        );
        $this->pageConfig->setMetadata(
            self::YANDEX_VERIFICATION,
            $this->helperData->getVerficationConfig('yandex')
        );
    }

    /**
     * Get full action name
     *
     * @return string
     */
    public function getFullActionName()
    {
        return $this->request->getFullActionName();
    }

    /**
     * Get current product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get Url
     *
     * @param string $route
     * @param array $params
     *
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * @param int $productId
     *
     * @return StockItemInterface
     * @throws NoSuchEntityException
     */
    public function getProductStock($productId)
    {
        return $this->stockItemRepository->get($productId);
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getReviewCount()
    {
        $ratingSummaries = $this->ratingFactory->create()->getEntitySummary($this->getProduct()->getId(), false);

        /** @var Rating $ratingSummary */
        foreach ($ratingSummaries as $ratingSummary) {
            if ($ratingSummary->getStoreId() === $this->_storeManager->getStore()->getId()) {
                return (int) $this->reviewResourceModel->getTotalReviews(
                    $this->getProduct()->getId(),
                    true,
                    $ratingSummary->getStoreId()
                );
            }
        }

        return 0;
    }

    /**
     * @return false|float|int|Rating|mixed
     * @throws NoSuchEntityException
     */
    public function getRatingSummary()
    {
        $ratingSummaries = $this->ratingFactory->create()->getEntitySummary($this->getProduct()->getId(), false);

        /** @var Rating $ratingSummary */
        foreach ($ratingSummaries as $ratingSummary) {
            if ($ratingSummary->getStoreId() === $this->_storeManager->getStore()->getId()) {
                if ($ratingSummary->getCount()) {
                    $ratingSummary = round($ratingSummary->getSum() / $ratingSummary->getCount());
                } else {
                    $ratingSummary = $ratingSummary->getSum();
                }

                return $ratingSummary;
            }
        }

        return 0;
    }

    /**
     * @param Product $product
     *
     * @throws NoSuchEntityException
     */
    public function getEntitySummary($product)
    {
        $this->reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
    }

    /**
     * Show product structured data
     * @return string
     *
     * Learn more: https://developers.google.com/structured-data/rich-snippets/products#single_product_page
     */
    public function showProductStructuredData()
    {
        if ($currentProduct = $this->getProduct()) {
            try {
                $productId = $currentProduct->getId() ?: $this->request->getParam('id');

                $product         = $this->productFactory->create()->load($productId);
                $availability    = $product->isAvailable() ? 'InStock' : 'OutOfStock';
                $stockItem       = $this->stockState->getStockItem(
                    $product->getId(),
                    $product->getStore()->getWebsiteId()
                );
                $priceValidUntil = $currentProduct->getSpecialToDate();
                $modelValue      = $product->getResource()
                    ->getAttribute($this->helperData->getRichsnippetsConfig('model_value'))
                    ->getFrontend()->getValue($product);
                $modelName       = $this->helperData->getRichsnippetsConfig('model_name');

                $productStructuredData = [
                    '@context'    => 'http://schema.org/',
                    '@type'       => 'Product',
                    'name'        => $currentProduct->getName(),
                    'description' => trim(strip_tags($currentProduct->getDescription())),
                    'sku'         => $currentProduct->getSku(),
                    'url'         => $currentProduct->getProductUrl(),
                    'image'       => $this->getUrl('pub/media/catalog') . 'product' . $currentProduct->getImage(),
                    'offers'      => [
                        '@type'         => 'Offer',
                        'priceCurrency' => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                        'price'         => $currentProduct->getPriceInfo()->getPrice('final_price')->getValue(),
                        'itemOffered'   => $stockItem->getQty(),
                        'availability'  => 'http://schema.org/' . $availability,
                        'url'           => $currentProduct->getProductUrl()
                    ],
                    $modelName    => $modelValue ?: $modelName
                ];
                $productStructuredData = $this->addProductStructuredDataByType(
                    $currentProduct->getTypeId(),
                    $currentProduct,
                    $productStructuredData
                );

                $priceValidType = $this->helperData->getRichsnippetsConfig('price_valid_until');
                if (!empty($priceValidUntil)) {
                    $productStructuredData['offers']['priceValidUntil'] = $priceValidUntil;
                } elseif ($priceValidType !== 'none') {
                    $time = $this->_dateTime->gmtTimestamp();

                    switch ($priceValidType) {
                        case PriceValidUntil::PLUS_7:
                            $time += 604800;
                            break;
                        case PriceValidUntil::PLUS_30:
                            $time += 2592000;
                            break;
                        case PriceValidUntil::PLUS_60:
                            $time += 5184000;
                            break;
                        case PriceValidUntil::PLUS_1_YEAR:
                            $time += 31536000;
                            break;
                        default:
                            $time = $this->helperData->getRichsnippetsConfig('price_valid_until_custom');
                            break;
                    }

                    $productStructuredData['offers']['priceValidUntil'] = $priceValidType === 'custom'
                        ? $time
                        : date('Y-m-d', $time);
                }

                if (!$this->_moduleManager->isEnabled('Mageplaza_Shopbybrand')) {
                    $brandValue = $product->getResource()
                        ->getAttribute($this->helperData->getRichsnippetsConfig('brand'))
                        ->getFrontend()->getValue($product);

                    $productStructuredData['brand']['@type'] = 'Thing';
                    $productStructuredData['brand']['name']  = $brandValue ?: 'Brand';
                }

                $collection = $this->_reviewCollection->create()
                    ->addStatusFilter(
                        Review::STATUS_APPROVED
                    )->addEntityFilter(
                        'product',
                        $product->getId()
                    )->setDateOrder();
                if ($collection->getData()) {
                    foreach ($collection->getData() as $review) {
                        $productStructuredData['review'][] = [
                            '@type'  => 'Review',
                            'author' => $review['nickname']
                        ];
                    }
                }

                if ($this->getReviewCount()) {
                    $productStructuredData['aggregateRating']['@type']       = 'AggregateRating';
                    $productStructuredData['aggregateRating']['bestRating']  = 100;
                    $productStructuredData['aggregateRating']['worstRating'] = 0;
                    $productStructuredData['aggregateRating']['ratingValue'] = $this->getRatingSummary();
                    $productStructuredData['aggregateRating']['reviewCount'] = $this->getReviewCount();
                }

                $objectStructuredData = new DataObject(['mpdata' => $productStructuredData]);
                $this->_eventManager->dispatch(
                    'mp_seo_product_structured_data',
                    ['structured_data' => $objectStructuredData]
                );
                $productStructuredData = $objectStructuredData->getMpdata();

                // Compatible with Mageplaza Shop By Brand
                if (!isset($productStructuredData['brand'])) {
                    $brandValue = $product->getResource()
                        ->getAttribute($this->helperData->getRichsnippetsConfig('brand'))
                        ->getFrontend()->getValue($product);

                    $productStructuredData['brand']['@type'] = 'Thing';
                    $productStructuredData['brand']['name']  = $brandValue ?: 'Brand';
                }

                return $this->helperData->createStructuredData(
                    $productStructuredData,
                    '<!-- Product Structured Data by Mageplaza SEO-->'
                );
            } catch (Exception $e) {
                $this->messageManager->addError(__('Can not add structured data'));
            }
        }
    }

    /**
     * get Business Structured Data
     *
     * @return string
     */
    public function showBusinessStructuredData()
    {
        $businessStructuredData = [
            '@context'     => 'http://schema.org/',
            '@type'        => 'Organization',
            'url'          => $this->getUrl(),
            'logo'         => $this->helperData->getLogo(),
            'name'         => $this->helperData->getInfoConfig('business_name'),
            'contactPoint' => []
        ];
        if (!empty($this->getSocialProfiles())) {
            $businessStructuredData['sameAs'] = $this->getSocialProfiles();
        }

        // get customer service info
        if ($this->helperData->getInfoConfig('customer_service_phone')
            || $this->helperData->getInfoConfig('customer_service_contact_option')
            || $this->helperData->getInfoConfig('customer_service_area_serve')
        ) {
            $businessStructuredData['contactPoint'][] = [
                '@type'         => 'ContactPoint',
                'telephone'     => $this->helperData->getInfoConfig('customer_service_phone'),
                'contactType'   => 'customer service',
                'contactOption' => $this->helperData->getInfoConfig('customer_service_contact_option'),
                'areaServed'    => $this->helperData->getInfoConfig('customer_service_area_serve')
            ];
        }
        // get technical support info
        if ($this->helperData->getInfoConfig('technical_support_phone')
            || $this->helperData->getInfoConfig('technical_support_contact_option')
            || $this->helperData->getInfoConfig('technical_support_area_serve')
        ) {
            $businessStructuredData['contactPoint'][] = [
                '@type'         => 'ContactPoint',
                'telephone'     => $this->helperData->getInfoConfig('technical_support_phone'),
                'contactType'   => 'technical support',
                'contactOption' => $this->helperData->getInfoConfig('technical_support_contact_option'),
                'areaServed'    => $this->helperData->getInfoConfig('technical_support_area_serve')
            ];
        }
        // get sales info
        if ($this->helperData->getInfoConfig('sales_phone')
            || $this->helperData->getInfoConfig('sales_contact_option')
            || $this->helperData->getInfoConfig('sales_area_serve')
        ) {
            $businessStructuredData['contactPoint'][] = [
                '@type'         => 'ContactPoint',
                'telephone'     => $this->helperData->getInfoConfig('sales_phone'),
                'contactType'   => 'sales',
                'contactOption' => $this->helperData->getInfoConfig('sales_contact_option'),
                'areaServed'    => $this->helperData->getInfoConfig('sales_area_serve')
            ];
        }

        return $this->helperData->createStructuredData(
            $businessStructuredData,
            '<!-- Business Structured Data by Mageplaza SEO-->'
        );
    }

    /**
     * get Social Profiles config
     *
     * @return array|string
     */

    public function getSocialProfiles()
    {
        $lines         = [];
        $socialNetwork = [
            'facebook',
            'twitter',
            'google',
            'instagram',
            'youtube',
            'linkedin',
            'myspace',
            'pinterest',
            'soundclound',
            'tumblr'
        ];
        foreach ($socialNetwork as $value) {
            if ($profile = $this->helperData->getSocialProfiles($value)) {
                $lines[] = $profile;
            }
        }

        return $lines;
    }

    /**
     * get Sitelinks Searchbox Structured Data
     *
     * @return string
     */
    public function showSiteLinksStructuredData()
    {
        $siteLinksStructureData = [
            '@context'        => 'http://schema.org',
            '@type'           => 'WebSite',
            'url'             => $this->_urlBuilder->getBaseUrl(),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => $this->_searchHelper->getResultUrl() . '?q={searchbox_target}',
                'query-input' => 'required name=searchbox_target'
            ]
        ];

        return $this->helperData->createStructuredData(
            $siteLinksStructureData,
            '<!-- Sitelinks Searchbox Structured Data by Mageplaza SEO-->'
        );
    }

    /**
     * add Grouped Product Structured Data
     *
     * @param Product $currentProduct
     * @param array $productStructuredData
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getGroupedProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type'] = 'AggregateOffer';
        $childrenPrice                            = [];
        $offerData                                = [];
        $typeInstance                             = $currentProduct->getTypeInstance();
        $childProductCollection                   = $typeInstance->getAssociatedProducts($currentProduct);
        foreach ($childProductCollection as $child) {
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product' . $child->getImage();

            $offerData[]     = [
                '@type' => 'Offer',
                'name'  => $child->getName(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false),
                'sku'   => $child->getSku(),
                'image' => $imageUrl
            ];
            $childrenPrice[] = $this->_priceHelper->currency($child->getPrice(), false);
        }

        $productStructuredData['offers']['highPrice'] = array_sum($childrenPrice);
        $productStructuredData['offers']['lowPrice']  = $childrenPrice ? min($childrenPrice) : 0;
        unset($productStructuredData['offers']['price']);

        if (!empty($offerData)) {
            $productStructuredData['offers']['offerCount'] = count($offerData);
            $productStructuredData['offers']['offers']     = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * add Downloadable Product Structured Data
     *
     * @param Product $currentProduct
     * @param array $productStructuredData
     *
     * @return mixed
     */
    public function getDownloadableProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type'] = 'AggregateOffer';

        $typeInstance           = $currentProduct->getTypeInstance();
        $childProductCollection = $typeInstance->getLinks($currentProduct);
        $childrenPrice          = [];
        foreach ($childProductCollection as $child) {
            $offerData[]     = [
                '@type' => 'Offer',
                'name'  => $child->getTitle(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false)
            ];
            $childrenPrice[] = $this->_priceHelper->currency($child->getPrice(), false);
        }
        $productStructuredData['offers']['highPrice'] = array_sum($childrenPrice);
        $productStructuredData['offers']['lowPrice']  = $childrenPrice ? min($childrenPrice) : 0;

        if (!empty($offerData)) {
            $productStructuredData['offers']['offerCount'] = count($offerData);
            $productStructuredData['offers']['offers']     = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * add Configurable Product Structured Data
     *
     * @param Product $currentProduct
     * @param array $productStructuredData
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfigurableProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type']     = 'AggregateOffer';
        try {
            $productStructuredData['offers']['highPrice'] = $currentProduct->getPriceInfo()->getPrice('regular_price')
                                                                ->getMaxRegularAmount()->getValue();
            $productStructuredData['offers']['lowPrice']  = $currentProduct->getPriceInfo()->getPrice('regular_price')
                                                                ->getMinRegularAmount()->getValue();
        } catch (Exception $exception) {
            $productStructuredData['offers']['highPrice'] = 0;
            $productStructuredData['offers']['lowPrice']  = 0;
        }
        $offerData                                    = [];
        $typeInstance                                 = $currentProduct->getTypeInstance();
        $childProductCollection                       = $typeInstance->getUsedProductCollection($currentProduct)
                                                            ->addAttributeToSelect('*');
        foreach ($childProductCollection as $child) {
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product' . $child->getImage();

            $offerData[] = [
                '@type' => 'Offer',
                'name'  => $child->getName(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false),
                'sku'   => $child->getSku(),
                'image' => $imageUrl
            ];
        }
        if (!empty($offerData)) {
            $productStructuredData['offers']['offerCount'] = count($offerData);
            $productStructuredData['offers']['offers']     = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * add Bundle Product Structured Data
     *
     * @param Product $currentProduct
     * @param array $productStructuredData
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getBundleProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type']     = 'AggregateOffer';
        try {
            $productStructuredData['offers']['highPrice'] = $currentProduct->getPriceInfo()->getPrice('regular_price')
                                                                ->getMaximalPrice()->getValue();
            $productStructuredData['offers']['lowPrice']  = $currentProduct->getPriceInfo()->getPrice('regular_price')
                                                                ->getMinimalPrice()->getValue();
        } catch (Exception $exception) {
            $productStructuredData['offers']['highPrice'] = 0;
            $productStructuredData['offers']['lowPrice']  = 0;
        }
        unset($productStructuredData['offers']['price']);
        $offerData              = [];
        $typeInstance           = $currentProduct->getTypeInstance();
        $childProductCollection = $typeInstance->getSelectionsCollection(
            $typeInstance->getOptionsIds($currentProduct),
            $currentProduct
        );
        foreach ($childProductCollection as $child) {
            $imageUrl    = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product' . $child->getImage();

            $offerData[] = [
                '@type' => 'Offer',
                'name'  => $child->getName(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false),
                'sku'   => $child->getSku(),
                'image' => $imageUrl
            ];
        }
        if (!empty($offerData)) {
            $productStructuredData['offers']['offerCount'] = count($offerData);
            $productStructuredData['offers']['offers']     = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * @param string $productType
     * @param Product $currentProduct
     * @param array $productStructuredData
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function addProductStructuredDataByType($productType, $currentProduct, $productStructuredData)
    {
        switch ($productType) {
            case 'grouped':
                $productStructuredData = $this->getGroupedProductStructuredData(
                    $currentProduct,
                    $productStructuredData
                );
                break;
            case 'bundle':
                $productStructuredData = $this->getBundleProductStructuredData($currentProduct, $productStructuredData);
                break;
            case 'downloadable':
                $productStructuredData = $this->getDownloadableProductStructuredData(
                    $currentProduct,
                    $productStructuredData
                );
                break;
            case 'configurable':
                $productStructuredData = $this->getConfigurableProductStructuredData(
                    $currentProduct,
                    $productStructuredData
                );
                break;
        }

        return $productStructuredData;
    }
}
