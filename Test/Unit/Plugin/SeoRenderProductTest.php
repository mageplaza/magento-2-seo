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

namespace Mageplaza\Seo\Test\Unit\Plugin;

use ArrayIterator;
use Exception;
use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Bundle\Pricing\Price\FinalPrice as BundleFinalPrice;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceInfoInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Review\Model\Rating;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Store\Model\Store;
use Mageplaza\Seo\Test\Unit\Stub\ProductStub;
use Mageplaza\Seo\Test\Unit\Traits\SeoRenderFactoryTrait;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class SeoRenderProductTest extends TestCase
{
    use SeoRenderFactoryTrait;

    private function priceInfo(float $final): PriceInfoInterface
    {
        $price = $this->createStub(PriceInterface::class);
        $price->method('getValue')->willReturn($final);
        $info = $this->createStub(PriceInfoInterface::class);
        $info->method('getPrice')->willReturn($price);

        return $info;
    }

    private function child(array $data): ProductStub
    {
        return new ProductStub($data);
    }

    public function testAddProductStructuredDataByTypeRoutesToTypeSpecificBuilder(): void
    {
        $product = new DataObject();
        $render = $this->buildPartialSeoRender([
            'getGroupedProductStructuredData',
            'getBundleProductStructuredData',
            'getDownloadableProductStructuredData',
            'getConfigurableProductStructuredData',
        ]);
        $render->method('getGroupedProductStructuredData')->willReturn(['by' => 'grouped']);
        $render->method('getBundleProductStructuredData')->willReturn(['by' => 'bundle']);
        $render->method('getDownloadableProductStructuredData')->willReturn(['by' => 'downloadable']);
        $render->method('getConfigurableProductStructuredData')->willReturn(['by' => 'configurable']);

        foreach (['grouped', 'bundle', 'downloadable', 'configurable'] as $type) {
            $this->assertSame(['by' => $type], $render->addProductStructuredDataByType($type, $product, ['x' => 1]));
        }
        $this->assertSame(['x' => 1], $render->addProductStructuredDataByType('simple', $product, ['x' => 1]));
        $this->assertSame(['x' => 1], $render->addProductStructuredDataByType('virtual', $product, ['x' => 1]));
    }

    public function testGroupedProductBecomesAggregateOfferWithChildOffers(): void
    {
        $render = $this->buildSeoRender();
        $typeInstance = $this->createStub(Grouped::class);
        $typeInstance->method('getAssociatedProducts')->willReturn([
            $this->child(['name' => 'A', 'sku' => 'a', 'final_price' => 10, 'image' => '/a.jpg']),
            $this->child(['name' => 'B', 'sku' => 'b', 'final_price' => 4, 'image' => '/b.jpg']),
        ]);
        $store = $this->createStub(Store::class);
        $store->method('getBaseUrl')->willReturn('https://shop.test/media/');
        $this->storeManagerMock->method('getStore')->willReturn($store);

        $data = $render->getGroupedProductStructuredData(
            new ProductStub([], null, $typeInstance),
            ['offers' => ['@type' => 'Offer', 'price' => 99]]
        );

        $offers = $data['offers'];
        $this->assertSame('AggregateOffer', $offers['@type']);
        $this->assertArrayNotHasKey('price', $offers);
        $this->assertSame(4, $offers['lowPrice']);
        $this->assertSame(2, $offers['offerCount']);
        $this->assertSame('https://shop.test/media/catalog/product/a.jpg', $offers['offers'][0]['image']);
        $this->assertSame(
            ['@type' => 'Offer', 'name' => 'B', 'price' => 4, 'sku' => 'b', 'image' => 'https://shop.test/media/catalog/product/b.jpg'],
            $offers['offers'][1]
        );
    }

    public function testGroupedProductWithoutChildrenHasZeroPricesAndNoOfferList(): void
    {
        $typeInstance = $this->createStub(Grouped::class);
        $typeInstance->method('getAssociatedProducts')->willReturn([]);

        $data = $this->buildSeoRender()->getGroupedProductStructuredData(
            new ProductStub([], null, $typeInstance),
            ['offers' => ['price' => 5]]
        );

        $this->assertSame(0, $data['offers']['lowPrice']);
        $this->assertSame(0, $data['offers']['highPrice']);
        $this->assertArrayNotHasKey('offers', $data['offers']);
        $this->assertArrayNotHasKey('offerCount', $data['offers']);
    }

    public function testGroupedChildWithoutImageFallsBackToImageHelper(): void
    {
        $render = $this->buildSeoRender();
        $typeInstance = $this->createStub(Grouped::class);
        $typeInstance->method('getAssociatedProducts')->willReturn([
            $this->child(['name' => 'A', 'sku' => 'a', 'final_price' => 10]),
        ]);
        $this->imageHelperMock->method('init')->willReturnSelf();
        $this->imageHelperMock->method('getUrl')->willReturn('https://shop.test/placeholder.png');

        $data = $render->getGroupedProductStructuredData(new ProductStub([], null, $typeInstance), ['offers' => []]);

        $this->assertSame('https://shop.test/placeholder.png', $data['offers']['offers'][0]['image']);
    }

    public function testBundleProductUsesMinimalAndMaximalFinalPrice(): void
    {
        $render = $this->buildSeoRender();
        $amount = function (float $value): AmountInterface {
            $amount = $this->createStub(AmountInterface::class);
            $amount->method('getValue')->willReturn($value);

            return $amount;
        };
        $finalPrice = $this->createStub(BundleFinalPrice::class);
        $finalPrice->method('getMaximalPrice')->willReturn($amount(150.0));
        $finalPrice->method('getMinimalPrice')->willReturn($amount(90.0));
        $info = $this->createStub(PriceInfoInterface::class);
        $info->method('getPrice')->willReturn($finalPrice);

        $typeInstance = $this->createStub(BundleType::class);
        $typeInstance->method('getOptionsIds')->willReturn([1, 2]);
        $typeInstance->method('getSelectionsCollection')->willReturn([
            $this->child(['name' => 'Sel', 'sku' => 's1', 'price' => 30, 'image' => '/s.jpg']),
        ]);
        $store = $this->createStub(Store::class);
        $store->method('getBaseUrl')->willReturn('https://shop.test/media/');
        $this->storeManagerMock->method('getStore')->willReturn($store);

        $data = $render->getBundleProductStructuredData(
            new ProductStub([], $info, $typeInstance),
            ['offers' => ['price' => 1]]
        );

        $this->assertSame('AggregateOffer', $data['offers']['@type']);
        $this->assertSame(150.0, $data['offers']['highPrice']);
        $this->assertSame(90.0, $data['offers']['lowPrice']);
        $this->assertArrayNotHasKey('price', $data['offers']);
        $this->assertSame(1, $data['offers']['offerCount']);
        $this->assertSame(30, $data['offers']['offers'][0]['price']);
    }

    public function testBundleProductPricesFallBackToZeroWhenPriceInfoFails(): void
    {
        $info = $this->createStub(PriceInfoInterface::class);
        $info->method('getPrice')->willThrowException(new Exception('no price'));
        $typeInstance = $this->createStub(BundleType::class);
        $typeInstance->method('getSelectionsCollection')->willReturn([]);

        $data = $this->buildSeoRender()->getBundleProductStructuredData(
            new ProductStub([], $info, $typeInstance),
            ['offers' => ['price' => 1]]
        );

        $this->assertSame(0, $data['offers']['highPrice']);
        $this->assertSame(0, $data['offers']['lowPrice']);
        $this->assertArrayNotHasKey('offers', $data['offers']);
    }

    public function testDownloadableProductListsLinksWithPriceRange(): void
    {
        $typeInstance = $this->createStub(DownloadableType::class);
        $typeInstance->method('getLinks')->willReturn([
            new DataObject(['title' => 'Basic', 'price' => 5]),
            new DataObject(['title' => 'Pro', 'price' => 20]),
        ]);

        $data = $this->buildSeoRender()->getDownloadableProductStructuredData(
            new ProductStub([], null, $typeInstance),
            ['offers' => []]
        );

        $this->assertSame('AggregateOffer', $data['offers']['@type']);
        $this->assertSame(5, $data['offers']['lowPrice']);
        $this->assertSame(25, $data['offers']['highPrice']);
        $this->assertSame(2, $data['offers']['offerCount']);
        $this->assertSame(
            [['@type' => 'Offer', 'name' => 'Basic', 'price' => 5], ['@type' => 'Offer', 'name' => 'Pro', 'price' => 20]],
            $data['offers']['offers']
        );
    }

    public function testDownloadableProductWithoutLinksHasZeroPrices(): void
    {
        $typeInstance = $this->createStub(DownloadableType::class);
        $typeInstance->method('getLinks')->willReturn([]);

        $data = $this->buildSeoRender()->getDownloadableProductStructuredData(
            new ProductStub([], null, $typeInstance),
            ['offers' => []]
        );

        $this->assertSame(0, $data['offers']['highPrice']);
        $this->assertSame(0, $data['offers']['lowPrice']);
        $this->assertArrayNotHasKey('offerCount', $data['offers']);
    }

    public function testConfigurableProductUsesMinAndMaxChildPrice(): void
    {
        $render = $this->buildSeoRender();
        $children = $this->createMock(ProductCollection::class);
        $children->expects($this->once())->method('addAttributeToSelect')->with('*')->willReturnSelf();
        $children->method('getIterator')->willReturn(new ArrayIterator([
            $this->child(['name' => 'S', 'sku' => 'c-s', 'price' => 12, 'image' => '/s.jpg']),
            $this->child(['name' => 'M', 'sku' => 'c-m', 'price' => 30, 'image' => '/m.jpg']),
            $this->child(['name' => 'L', 'sku' => 'c-l', 'price' => 18, 'image' => '/l.jpg']),
        ]));
        $typeInstance = $this->createStub(Configurable::class);
        $typeInstance->method('getUsedProductCollection')->willReturn($children);
        $store = $this->createStub(Store::class);
        $store->method('getBaseUrl')->willReturn('https://shop.test/media/');
        $this->storeManagerMock->method('getStore')->willReturn($store);

        $data = $render->getConfigurableProductStructuredData(
            new ProductStub([], null, $typeInstance),
            ['offers' => []]
        );

        $this->assertSame('AggregateOffer', $data['offers']['@type']);
        $this->assertSame(30, $data['offers']['highPrice']);
        $this->assertSame(12, $data['offers']['lowPrice']);
        $this->assertSame(3, $data['offers']['offerCount']);
        $this->assertSame(['c-s', 'c-m', 'c-l'], array_column($data['offers']['offers'], 'sku'));
    }

    public function testConfigurableProductWithoutChildrenHasZeroPrices(): void
    {
        $children = $this->createStub(ProductCollection::class);
        $children->method('addAttributeToSelect')->willReturnSelf();
        $children->method('getIterator')->willReturn(new ArrayIterator([]));
        $typeInstance = $this->createStub(Configurable::class);
        $typeInstance->method('getUsedProductCollection')->willReturn($children);

        $data = $this->buildSeoRender()->getConfigurableProductStructuredData(
            new ProductStub([], null, $typeInstance),
            ['offers' => []]
        );

        $this->assertSame(0, $data['offers']['highPrice']);
        $this->assertSame(0, $data['offers']['lowPrice']);
        $this->assertArrayNotHasKey('offerCount', $data['offers']);
    }

    #[DataProvider('reviewSummaryProvider')]
    public function testReviewCountAndRatingSummaryMatchCurrentStoreOnly(
        array $summaries,
        int $expectedCount,
        float|int $expectedRating
    ): void {
        $render = $this->buildSeoRender();
        $this->registryMock->method('registry')->with('current_product')->willReturn(new DataObject(['id' => 42]));
        $store = $this->createStub(Store::class);
        $store->method('getId')->willReturn(1);
        $this->storeManagerMock->method('getStore')->willReturn($store);
        $rating = $this->createMock(Rating::class);
        $rating->method('getEntitySummary')->with(42, false)->willReturn(
            array_map(static fn (array $row): DataObject => new DataObject($row), $summaries)
        );
        $this->ratingFactoryMock->method('create')->willReturn($rating);
        $this->reviewResourceMock->method('getTotalReviews')->with(42, true, 1)->willReturn('7');

        $this->assertSame($expectedCount, $render->getReviewCount());
        $this->assertEquals($expectedRating, $render->getRatingSummary());
    }

    /**
     * @return array<string, array{0: array<int, array<string, int>>, 1: int, 2: float|int}>
     */
    public static function reviewSummaryProvider(): array
    {
        return [
            'no summaries' => [[], 0, 0],
            'only other store' => [[['store_id' => 2, 'count' => 3, 'sum' => 240]], 0, 0],
            'current store with count' => [[['store_id' => 1, 'count' => 3, 'sum' => 250]], 7, 83],
            'current store, count zero keeps sum' => [[['store_id' => 1, 'count' => 0, 'sum' => 60]], 7, 60],
            'other store first' => [
                [['store_id' => 2, 'count' => 1, 'sum' => 10], ['store_id' => 1, 'count' => 2, 'sum' => 150]],
                7,
                75
            ],
        ];
    }

    private function buildProductFlow(array $options = []): array
    {
        $render = $this->buildSeoRender();
        $this->config['rich'] = array_merge([
            'model_value' => 'sku',
            'model_name' => 'mpn',
            'brand' => 'manufacturer',
            'price_valid_until' => 'none',
        ], $options['rich'] ?? []);
        $this->config['shipping'] = $options['shipping'] ?? [];
        $this->config['return'] = $options['return'] ?? [];

        $store = $this->createStub(Store::class);
        $store->method('getId')->willReturn(1);
        $store->method('getCurrentCurrencyCode')->willReturn('USD');
        $store->method('getWebsiteId')->willReturn(1);
        $this->storeManagerMock->method('getStore')->willReturn($store);

        $attributes = [
            'sku' => 'MPN-123',
            'manufacturer' => 'Acme',
            'meta_title' => 'Meta brand',
        ];
        $resource = $this->createStub(ProductResource::class);
        $resource->method('getAttribute')->willReturnCallback(
            function (string $code) use ($attributes): AbstractAttribute {
                $frontend = $this->createStub(AbstractFrontend::class);
                $frontend->method('getValue')->willReturn($attributes[$code] ?? null);
                $attribute = $this->createStub(AbstractAttribute::class);
                $attribute->method('getFrontend')->willReturn($frontend);

                return $attribute;
            }
        );

        $product = new ProductStub(
            array_merge([
                'id' => 42,
                'name' => 'Tee',
                'description' => ' <p>Soft <b>cotton</b></p> ',
                'sku' => 'TEE-1',
                'product_url' => 'https://shop.test/tee.html',
                'type_id' => 'simple',
                'image' => '/t/e/tee.jpg',
            ], $options['product'] ?? []),
            $this->priceInfo(19.9),
            null,
            $resource,
            $store,
            $options['available'] ?? true
        );
        $this->registryMock->method('registry')->willReturnMap([
            ['current_product', $product],
            ['meta_title', $options['meta_title'] ?? null],
        ]);
        $this->urlBuilderMock->method('getUrl')->willReturn('https://shop.test/pub/media/catalog');
        $this->productFactoryMock->method('create')->willReturn($product);
        $this->moduleManagerMock->method('isEnabled')->willReturn(false);

        $attributeCollection = $this->createStub(AttributeCollection::class);
        $attributeCollection->method('addVisibleFilter')->willReturnSelf();
        $attributeCollection->method('addFieldToFilter')->willReturnSelf();
        $attributeCollection->method('getColumnValues')->willReturn(['price', 'special_price']);
        $this->attributeCollectionFactoryMock->method('create')->willReturn($attributeCollection);

        $stockItem = $this->createStub(StockItemInterface::class);
        $stockItem->method('getQty')->willReturn(8.0);
        $this->stockStateMock->method('getStockItem')->willReturn($stockItem);
        $this->sourceItemsMock->method('execute')->willReturn([]);

        $reviews = $this->createMock(ReviewCollection::class);
        foreach (['addStatusFilter', 'addEntityFilter', 'addStoreFilter', 'addRateVotes', 'setDateOrder'] as $method) {
            $reviews->method($method)->willReturnSelf();
        }
        $reviews->method('getSize')->willReturn(count($options['reviews'] ?? []));
        $reviews->method('getIterator')->willReturn(new ArrayIterator($options['reviews'] ?? []));
        $this->reviewCollectionFactoryMock->method('create')->willReturn($reviews);

        $rating = $this->createStub(Rating::class);
        $rating->method('getEntitySummary')->willReturn($options['summaries'] ?? []);
        $this->ratingFactoryMock->method('create')->willReturn($rating);
        $this->reviewResourceMock->method('getTotalReviews')->willReturn($options['total_reviews'] ?? 0);

        return [$render, $product];
    }

    public function testShowProductStructuredDataBuildsSimpleProductSchema(): void
    {
        [$render] = $this->buildProductFlow();

        $html = $render->showProductStructuredData();

        $this->assertStringStartsWith('<!-- Product Structured Data by Mageplaza SEO-->', $html);
        $data = $this->extractJson($html);
        $this->assertSame('Product', $data['@type']);
        $this->assertSame('Tee', $data['name']);
        $this->assertSame('Soft cotton', $data['description']);
        $this->assertSame('TEE-1', $data['sku']);
        $this->assertSame('https://shop.test/tee.html', $data['url']);
        $this->assertSame('https://shop.test/pub/media/catalogproduct/t/e/tee.jpg', $data['image']);
        $this->assertSame('MPN-123', $data['mpn']);
        $this->assertSame(['@type' => 'Brand', 'name' => 'Acme'], $data['brand']);
        $this->assertEquals(19.9, $data['offers']['price']);
        $this->assertSame('USD', $data['offers']['priceCurrency']);
        $this->assertEquals(8, $data['offers']['itemOffered']);
        $this->assertSame('http://schema.org/InStock', $data['offers']['availability']);
        $this->assertArrayNotHasKey('priceValidUntil', $data['offers']);
        $this->assertArrayNotHasKey('shippingDetails', $data['offers']);
        $this->assertArrayNotHasKey('hasMerchantReturnPolicy', $data['offers']);
        $this->assertArrayNotHasKey('review', $data);
        $this->assertArrayNotHasKey('aggregateRating', $data);
    }

    public function testShowProductStructuredDataFallsBackToImageHelperWhenProductHasNoImage(): void
    {
        [$render] = $this->buildProductFlow(['product' => ['image' => null, 'description' => null]]);
        $this->imageHelperMock->method('init')->willReturnSelf();
        $this->imageHelperMock->method('getUrl')->willReturn('https://shop.test/placeholder.png');

        $data = $this->extractJson($render->showProductStructuredData());

        $this->assertSame('https://shop.test/placeholder.png', $data['image']);
        $this->assertSame('', $data['description']);
    }

    public function testShowProductStructuredDataMarksUnavailableProductOutOfStock(): void
    {
        [$render] = $this->buildProductFlow(['available' => false]);

        $data = $this->extractJson($render->showProductStructuredData());

        $this->assertSame('http://schema.org/OutOfStock', $data['offers']['availability']);
    }

    #[DataProvider('priceValidUntilProvider')]
    public function testShowProductStructuredDataPriceValidUntil(
        string $type,
        ?string $specialToDate,
        ?string $custom,
        mixed $expected
    ): void {
        [$render] = $this->buildProductFlow([
            'rich' => ['price_valid_until' => $type, 'price_valid_until_custom' => $custom],
            'product' => ['special_to_date' => $specialToDate],
        ]);
        $this->dateTimeMock->method('gmtTimestamp')->willReturn(strtotime('2026-01-01 12:00:00 UTC'));

        $data = $this->extractJson($render->showProductStructuredData());

        if ($expected === null) {
            $this->assertArrayNotHasKey('priceValidUntil', $data['offers']);
        } else {
            $this->assertSame($expected, $data['offers']['priceValidUntil']);
        }
    }

    /**
     * @return array<string, array{0: string, 1: ?string, 2: ?string, 3: mixed}>
     */
    public static function priceValidUntilProvider(): array
    {
        return [
            'none' => ['none', null, null, null],
            '+7 days' => ['7days', null, null, '2026-01-08'],
            '+30 days' => ['30days', null, null, '2026-01-31'],
            '+60 days' => ['60days', null, null, '2026-03-02'],
            '+1 year' => ['1year', null, null, '2027-01-01'],
            'custom date is passed through' => ['custom', null, '2026-12-31', '2026-12-31'],
            'special_to_date wins over type' => ['7days', '2026-05-05 00:00:00', null, '2026-05-05 00:00:00'],
            'special_to_date wins over none' => ['none', '2026-05-05 00:00:00', null, '2026-05-05 00:00:00'],
        ];
    }

    public function testShowProductStructuredDataAddsShippingAndReturnPolicyWhenEnabled(): void
    {
        [$render] = $this->buildProductFlow([
            'shipping' => [
                'enable_shipping_details' => '1',
                'shipping_fee' => '3.5',
                'shipping_currency' => 'USD',
                'shipping_country' => 'US',
            ],
            'return' => ['enable_mrp' => '1', 'refund_type' => 'FullRefund'],
        ]);

        $offers = $this->extractJson($render->showProductStructuredData())['offers'];

        $this->assertSame('OfferShippingDetails', $offers['shippingDetails']['@type']);
        $this->assertSame('3.5', $offers['shippingDetails']['shippingRate']['value']);
        $this->assertSame('MerchantReturnPolicy', $offers['hasMerchantReturnPolicy']['@type']);
        $this->assertSame('https://schema.org/FullRefund', $offers['hasMerchantReturnPolicy']['refundType']);
    }

    #[DataProvider('brandProvider')]
    public function testShowProductStructuredDataBrandSource(
        string $brandAttribute,
        ?string $registryMetaTitle,
        string $expectedBrand
    ): void {
        [$render] = $this->buildProductFlow([
            'rich' => ['brand' => $brandAttribute],
            'meta_title' => $registryMetaTitle,
            'product' => ['meta_title' => 'Product meta title'],
        ]);

        $data = $this->extractJson($render->showProductStructuredData());

        $this->assertSame($expectedBrand, $data['brand']['name']);
    }

    /**
     * @return array<string, array{0: string, 1: ?string, 2: string}>
     */
    public static function brandProvider(): array
    {
        return [
            'attribute value' => ['manufacturer', null, 'Acme'],
            'attribute without value falls back to Brand' => ['color', null, 'Brand'],
            'meta_title from registry' => ['meta_title', 'Registry title', 'Registry title'],
            'meta_title from product' => ['meta_title', null, 'Product meta title'],
        ];
    }

    public function testShowProductStructuredDataAppendsApprovedReviewsAndAggregateRating(): void
    {
        $reviews = [
            new DataObject([
                'nickname' => 'Ann',
                'rating_votes' => new DataObject([['percent' => 80]]),
            ]),
            new DataObject([
                'nickname' => 'Bob',
                'rating_votes' => new DataObject([]),
            ]),
        ];
        [$render] = $this->buildProductFlow([
            'reviews' => $reviews,
            'summaries' => [new DataObject(['store_id' => 1, 'count' => 2, 'sum' => 150])],
            'total_reviews' => 2,
        ]);

        $data = $this->extractJson($render->showProductStructuredData());

        $this->assertSame(
            [
                '@type' => 'Review',
                'author' => ['@type' => 'Person', 'name' => 'Ann'],
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => 80, 'bestRating' => '100'],
            ],
            $data['review'][0]
        );
        $this->assertSame(['@type' => 'Person', 'name' => 'Bob'], $data['review'][1]['author']);
        $this->assertArrayNotHasKey('reviewRating', $data['review'][1]);
        $this->assertEquals(
            [
                '@type' => 'AggregateRating',
                'bestRating' => 100,
                'worstRating' => 0,
                'ratingValue' => 75,
                'reviewCount' => 2,
            ],
            $data['aggregateRating']
        );
    }

    public function testShowProductStructuredDataLetsListenersModifyPayload(): void
    {
        [$render] = $this->buildProductFlow();
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with('mp_seo_product_structured_data', $this->arrayHasKey('structured_data'))
            ->willReturnCallback(static function (string $event, array $data): void {
                $payload = $data['structured_data'];
                $mpdata = $payload->getMpdata();
                $mpdata['gtin13'] = '4006381333931';
                $payload->setMpdata($mpdata);
            });

        $data = $this->extractJson($render->showProductStructuredData());

        $this->assertSame('4006381333931', $data['gtin13']);
        $this->assertSame('Product', $data['@type']);
    }

    public function testShowProductStructuredDataReturnsEmptyStringWithoutCurrentProduct(): void
    {
        $render = $this->buildSeoRender();
        $this->registryMock->method('registry')->willReturn(null);
        $this->productFactoryMock->expects($this->never())->method('create');

        $this->assertSame('', $render->showProductStructuredData());
    }

    public function testShowProductStructuredDataSwallowsExceptionAndAddsErrorMessage(): void
    {
        $render = $this->buildSeoRender();
        $this->registryMock->method('registry')->willReturn(new ProductStub(['id' => 1]));
        $this->attributeCollectionFactoryMock->method('create')->willThrowException(new Exception('db down'));
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with($this->callback(static fn ($message): bool => (string) $message === 'Can not add structured data'));

        $this->assertSame('', $render->showProductStructuredData());
    }
}
