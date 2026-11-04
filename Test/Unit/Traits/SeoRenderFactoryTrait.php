<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Seo
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

declare(strict_types=1);

namespace Mageplaza\Seo\Test\Unit\Traits;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Manager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface as SourceItems;
use Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite as AssignedStock;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku as SalableQuantity;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\ResourceModel\Review as ReviewResourceModel;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollection;
use Magento\Review\Model\ReviewFactory;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Seo\Helper\Data as HelperData;
use Mageplaza\Seo\Plugin\SeoRender;
use PHPUnit\Framework\MockObject\MockObject;

trait SeoRenderFactoryTrait
{
    protected array $config = [];

    protected HelperData|MockObject $helperMock;

    protected PageConfig|MockObject $pageConfigMock;

    protected Http|MockObject $requestMock;

    protected Registry|MockObject $registryMock;

    protected StoreManagerInterface|MockObject $storeManagerMock;

    protected UrlInterface|MockObject $urlBuilderMock;

    protected ProductFactory|MockObject $productFactoryMock;

    protected ManagerInterface|MockObject $messageManagerMock;

    protected StockRegistryInterface|MockObject $stockStateMock;

    protected SearchHelper|MockObject $searchHelperMock;

    protected PriceHelper|MockObject $priceHelperMock;

    protected Manager|MockObject $eventManagerMock;

    protected DateTime|MockObject $dateTimeMock;

    protected ReviewCollection|MockObject $reviewCollectionFactoryMock;

    protected ModuleManager|MockObject $moduleManagerMock;

    protected RatingFactory|MockObject $ratingFactoryMock;

    protected ReviewResourceModel|MockObject $reviewResourceMock;

    protected CollectionFactory|MockObject $attributeCollectionFactoryMock;

    protected SourceItems|MockObject $sourceItemsMock;

    protected AssignedStock|MockObject $assignedStockMock;

    protected SalableQuantity|MockObject $salableQuantityMock;

    protected ImageHelper|MockObject $imageHelperMock;

    protected function configValue(string $group, ?string $code): mixed
    {
        return $this->config[$group][$code] ?? null;
    }

    protected function createSeoRenderArguments(): array
    {
        $this->helperMock = $this->getMockBuilder(HelperData::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isEnabled', 'getStoreId', 'getLogo', 'getQtySale', 'getConfigValue',
                'getVerficationConfig', 'getRichsnippetsConfig', 'getInfoConfig', 'getSocialProfiles',
                'getShippingDetailConfig', 'getReturnPolicyConfig'
            ])
            ->getMock();
        $this->helperMock->method('isEnabled')->willReturnCallback(
            fn () => $this->config['enabled'] ?? true
        );
        $this->helperMock->method('getStoreId')->willReturn(1);
        $this->helperMock->method('getLogo')->willReturn('https://shop.test/logo.png');
        $this->helperMock->method('getQtySale')->willReturnCallback(
            fn () => $this->config['qty_sale'] ?? 0
        );
        $this->helperMock->method('getConfigValue')->willReturnCallback(
            fn ($path) => $this->configValue('general', $path)
        );
        foreach ([
            'getVerficationConfig' => 'verification',
            'getRichsnippetsConfig' => 'rich',
            'getInfoConfig' => 'info',
            'getSocialProfiles' => 'social',
            'getShippingDetailConfig' => 'shipping',
            'getReturnPolicyConfig' => 'return',
        ] as $method => $group) {
            $this->helperMock->method($method)->willReturnCallback(
                fn ($code = null) => $this->configValue($group, $code)
            );
        }

        $this->pageConfigMock = $this->createMock(PageConfig::class);
        $this->requestMock = $this->createMock(Http::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->urlBuilderMock = $this->createMock(UrlInterface::class);
        $this->productFactoryMock = $this->createMock(ProductFactory::class);
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);
        $this->stockStateMock = $this->createMock(StockRegistryInterface::class);
        $this->searchHelperMock = $this->createMock(SearchHelper::class);
        $this->priceHelperMock = $this->createMock(PriceHelper::class);
        $this->priceHelperMock->method('currency')->willReturnArgument(0);
        $this->eventManagerMock = $this->createMock(Manager::class);
        $this->dateTimeMock = $this->createMock(DateTime::class);
        $this->reviewCollectionFactoryMock = $this->createMock(ReviewCollection::class);
        $this->moduleManagerMock = $this->createMock(ModuleManager::class);
        $this->ratingFactoryMock = $this->createMock(RatingFactory::class);
        $this->reviewResourceMock = $this->createMock(ReviewResourceModel::class);
        $this->attributeCollectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->sourceItemsMock = $this->createMock(SourceItems::class);
        $this->assignedStockMock = $this->createMock(AssignedStock::class);
        $this->salableQuantityMock = $this->createMock(SalableQuantity::class);
        $this->imageHelperMock = $this->createMock(ImageHelper::class);

        return [
            $this->pageConfigMock,
            $this->requestMock,
            $this->helperMock,
            $this->createMock(StockItemRepository::class),
            $this->registryMock,
            $this->createMock(ReviewFactory::class),
            $this->storeManagerMock,
            $this->urlBuilderMock,
            $this->productFactoryMock,
            $this->messageManagerMock,
            $this->stockStateMock,
            $this->searchHelperMock,
            $this->priceHelperMock,
            $this->eventManagerMock,
            $this->dateTimeMock,
            $this->createMock(TimezoneInterface::class),
            $this->reviewCollectionFactoryMock,
            $this->moduleManagerMock,
            $this->ratingFactoryMock,
            $this->reviewResourceMock,
            $this->attributeCollectionFactoryMock,
            $this->sourceItemsMock,
            $this->assignedStockMock,
            $this->salableQuantityMock,
            $this->imageHelperMock,
        ];
    }

    protected function buildSeoRender(): SeoRender
    {
        return new SeoRender(...$this->createSeoRenderArguments());
    }

    /**
     * @param string[] $methods
     */
    protected function buildPartialSeoRender(array $methods): SeoRender|MockObject
    {
        return $this->getMockBuilder(SeoRender::class)
            ->setConstructorArgs($this->createSeoRenderArguments())
            ->onlyMethods($methods)
            ->getMock();
    }

    protected function extractJson(string $html): array
    {
        $this->assertMatchesRegularExpression('#<script type="application/ld\+json">(.*)</script>#s', $html);
        preg_match('#<script type="application/ld\+json">(.*)</script>#s', $html, $matches);

        return json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
    }
}
