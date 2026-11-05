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

namespace Mageplaza\Seo\Test\Unit\Helper;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Seo\Helper\Data;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class DataTest extends TestCase
{
    private StoreManagerInterface|MockObject $storeManagerMock;

    private StockItemRepository|MockObject $stockItemRepositoryMock;

    protected function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->stockItemRepositoryMock = $this->createMock(StockItemRepository::class);
    }

    /**
     * @param string[] $methods
     */
    private function createHelper(array $methods): Data|MockObject
    {
        return $this->getMockBuilder(Data::class)
            ->setConstructorArgs([
                $this->createMock(Context::class),
                $this->createMock(ObjectManagerInterface::class),
                $this->storeManagerMock,
                $this->stockItemRepositoryMock
            ])
            ->onlyMethods($methods)
            ->getMock();
    }

    #[DataProvider('configPathProvider')]
    public function testConfigGettersBuildPathFromCode(string $method, string $code, string $expectedPath): void
    {
        $helper = $this->createHelper(['getConfigValue']);
        $helper->expects($this->once())
            ->method('getConfigValue')
            ->with($expectedPath, 5)
            ->willReturn('value');

        $this->assertSame('value', $helper->$method($code, 5));
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function configPathProvider(): array
    {
        return [
            'meta with code' => ['getMetaConfig', 'robots', 'seo/meta/robots'],
            'meta empty code' => ['getMetaConfig', '', 'seo/meta'],
            'verification with code' => ['getVerficationConfig', 'google', 'seo/verification/google'],
            'verification empty code' => ['getVerficationConfig', '', 'seo/verification'],
            'richsnippets with code' => ['getRichsnippetsConfig', 'enable_product', 'seo/richsnippets/enable_product'],
            'richsnippets empty code' => ['getRichsnippetsConfig', '', 'seo/richsnippets'],
            'shipping with code' => [
                'getShippingDetailConfig',
                'shipping_fee',
                'seo/richsnippets/shipping_details/shipping_fee'
            ],
            'shipping empty code' => ['getShippingDetailConfig', '', 'seo/richsnippets/shipping_details'],
            'return policy with code' => [
                'getReturnPolicyConfig',
                'refund_type',
                'seo/richsnippets/merchant_return_policy/refund_type'
            ],
            'return policy empty code' => ['getReturnPolicyConfig', '', 'seo/richsnippets/merchant_return_policy'],
            'info with code' => ['getInfoConfig', 'business_name', 'seo/info/business_name'],
            'info empty code' => ['getInfoConfig', '', 'seo/info'],
            'social with code' => ['getSocialProfiles', 'facebook', 'seo/social_profiles/facebook'],
            'social empty code' => ['getSocialProfiles', '', 'seo/social_profiles'],
            'duplicate with code' => ['getDuplicateConfig', 'canonical_tag', 'seo/duplicate/canonical_tag'],
            'duplicate empty code' => ['getDuplicateConfig', '', 'seo/duplicate'],
        ];
    }

    public function testCreateStructuredDataWrapsJsonInScriptWithComments(): void
    {
        $helper = $this->createHelper([]);
        $data = ['@type' => 'Product', 'name' => 'Tee "Blue"', 'price' => 9.5];

        $html = $helper->createStructuredData($data, '<!-- start -->', '<!-- end -->');

        $this->assertStringStartsWith('<!-- start --><script type="application/ld+json">', $html);
        $this->assertStringEndsWith('</script><!-- end -->', $html);
        $json = substr($html, strlen('<!-- start --><script type="application/ld+json">'), -strlen('</script><!-- end -->'));
        $this->assertSame($data, json_decode($json, true));
        $this->assertStringContainsString("\n", $json);
    }

    public function testCreateStructuredDataWithoutCommentsHasNoPrefixOrSuffix(): void
    {
        $helper = $this->createHelper([]);

        $html = $helper->createStructuredData(['a' => 1]);

        $this->assertStringStartsWith('<script type="application/ld+json">', $html);
        $this->assertStringEndsWith('</script>', $html);
    }

    public function testGetStoreIdReturnsCurrentStoreId(): void
    {
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn(7);
        $this->storeManagerMock->method('getStore')->willReturn($store);

        $this->assertSame(7, $this->createHelper([])->getStoreId());
    }

    public function testGetStoreIdReturnsNullWhenStoreCannotBeResolved(): void
    {
        $this->storeManagerMock->method('getStore')->willThrowException(new Exception('no store'));

        $this->assertNull($this->createHelper([])->getStoreId());
    }

    #[DataProvider('canonicalProvider')]
    public function testCanUseCanonicalForCategoryAndProduct(
        ?string $config,
        bool $expectedCategory,
        bool $expectedProduct
    ): void {
        $helper = $this->createHelper(['getDuplicateConfig']);
        $helper->method('getDuplicateConfig')->with('canonical_tag', 3)->willReturn($config);

        $this->assertSame($expectedCategory, $helper->canUseCanonicalForCategory(3));
        $this->assertSame($expectedProduct, $helper->canUseCanonicalForProduct(3));
    }

    /**
     * @return array<string, array{0: ?string, 1: bool, 2: bool}>
     */
    public static function canonicalProvider(): array
    {
        return [
            'null' => [null, false, false],
            'empty string' => ['', false, false],
            'zero' => ['0', false, false],
            'category only' => ['1', true, false],
            'product only' => ['2', false, true],
            'both' => ['1,2', true, true],
            'both reversed' => ['2,1', true, true],
        ];
    }

    public function testGetQtySaleSumsSalableQtyOfSimpleProductWhenMsiAvailable(): void
    {
        $this->stockItemRepositoryMock->method('get')->willReturn($this->createMock(StockItemInterface::class));
        $salable = $this->createMock(GetSalableQuantityDataBySku::class);
        $salable->expects($this->once())->method('execute')->with('SKU-1')->willReturn([['qty' => 12.5]]);

        $helper = $this->createHelper(['versionCompare', 'createObject']);
        $helper->method('versionCompare')->with('2.3.0')->willReturn(true);
        $helper->method('createObject')->with(GetSalableQuantityDataBySku::class)->willReturn($salable);

        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(10);
        $product->method('getSku')->willReturn('SKU-1');
        $product->method('getTypeId')->willReturn('simple');

        $this->assertEquals(12.5, $helper->getQtySale($product));
    }

    public function testGetQtySaleReturnsZeroWhenSalableDataHasNoQty(): void
    {
        $this->stockItemRepositoryMock->method('get')->willReturn($this->createMock(StockItemInterface::class));
        $salable = $this->createMock(GetSalableQuantityDataBySku::class);
        $salable->method('execute')->willReturn([]);

        $helper = $this->createHelper(['versionCompare', 'createObject']);
        $helper->method('versionCompare')->willReturn(true);
        $helper->method('createObject')->willReturn($salable);

        $product = $this->createMock(Product::class);
        $product->method('getTypeId')->willReturn('simple');
        $product->method('getSku')->willReturn('SKU-1');

        $this->assertSame(0, $helper->getQtySale($product));
    }

    public function testGetQtySaleSumsChildrenQtyForConfigurableProduct(): void
    {
        $this->stockItemRepositoryMock->method('get')->willReturn($this->createMock(StockItemInterface::class));

        $childA = $this->createMock(Product::class);
        $childA->method('getSku')->willReturn('CHILD-A');
        $childB = $this->createMock(Product::class);
        $childB->method('getSku')->willReturn('CHILD-B');
        $childC = $this->createMock(Product::class);
        $childC->method('getSku')->willReturn('CHILD-C');

        $typeInstance = $this->createMock(Configurable::class);
        $typeInstance->method('getUsedProducts')->willReturn([$childA, $childB, $childC]);

        $salable = $this->createMock(GetSalableQuantityDataBySku::class);
        $salable->method('execute')->willReturnMap([
            ['CHILD-A', [['qty' => 4]]],
            ['CHILD-B', [['qty' => 6]]],
            ['CHILD-C', []],
        ]);

        $helper = $this->createHelper(['versionCompare', 'createObject']);
        $helper->method('versionCompare')->willReturn(true);
        $helper->method('createObject')->willReturn($salable);

        $product = $this->createMock(Product::class);
        $product->method('getTypeId')->willReturn(Configurable::TYPE_CODE);
        $product->method('getTypeInstance')->willReturn($typeInstance);

        $this->assertSame(10, $helper->getQtySale($product));
    }

    #[DataProvider('legacyStockProvider')]
    public function testGetQtySaleUsesStockItemWhenMsiNotAvailable(bool $inStock, float $qty, float|int $expected): void
    {
        $stock = $this->createMock(StockItemInterface::class);
        $stock->method('getIsInStock')->willReturn($inStock);
        $stock->method('getQty')->willReturn($qty);
        $this->stockItemRepositoryMock->method('get')->with(10)->willReturn($stock);

        $helper = $this->createHelper(['versionCompare']);
        $helper->method('versionCompare')->willReturn(false);

        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(10);

        $this->assertEquals($expected, $helper->getQtySale($product));
    }

    /**
     * @return array<string, array{0: bool, 1: float, 2: float|int}>
     */
    public static function legacyStockProvider(): array
    {
        return [
            'in stock' => [true, 8.0, 8.0],
            'out of stock' => [false, 8.0, 0],
        ];
    }

    public function testGetQtySaleReturnsZeroWhenStockLookupFails(): void
    {
        $this->stockItemRepositoryMock->method('get')->willThrowException(new Exception('missing'));

        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(99);

        $this->assertSame(0, $this->createHelper([])->getQtySale($product));
    }
}
