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

use ArrayObject;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config\Renderer;
use Magento\Store\Model\Store;
use Mageplaza\Seo\Test\Unit\Traits\SeoRenderFactoryTrait;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class SeoRenderTest extends TestCase
{
    use SeoRenderFactoryTrait;

    private function collectMetadata(): ArrayObject
    {
        $calls = new ArrayObject();
        $this->pageConfigMock->method('setMetadata')->willReturnCallback(
            static function (string $name, mixed $content) use ($calls): void {
                $calls[$name] = $content;
            }
        );

        return $calls;
    }

    public function testBeforeRenderMetadataSetsVerificationMetaTagsFromConfig(): void
    {
        $this->config['verification'] = [
            'google' => 'g-token',
            'bing' => 'b-token',
            'pinterest' => 'p-token',
            'yandex' => 'y-token',
        ];
        $render = $this->buildSeoRender();
        $this->requestMock->method('getFullActionName')->willReturn('cms_index_index');
        $calls = $this->collectMetadata();

        $render->beforeRenderMetadata($this->createStub(Renderer::class));

        $this->assertSame(
            [
                'google-site-verification' => 'g-token',
                'msvalidate.01' => 'b-token',
                'p:domain_verify' => 'p-token',
                'yandex-verification' => 'y-token',
            ],
            $calls->getArrayCopy()
        );
    }

    #[DataProvider('noindexPageProvider')]
    public function testBeforeRenderMetadataSetsNoindexOnlyForSearchAndNoRoutePages(
        string $action,
        bool $expectNoindex
    ): void {
        $render = $this->buildSeoRender();
        $this->requestMock->method('getFullActionName')->willReturn($action);
        $calls = $this->collectMetadata();

        $render->beforeRenderMetadata($this->createStub(Renderer::class));

        if ($expectNoindex) {
            $this->assertSame('NOINDEX,NOFOLLOW', $calls['robots']);
        } else {
            $this->assertArrayNotHasKey('robots', $calls->getArrayCopy());
        }
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function noindexPageProvider(): array
    {
        return [
            'search result' => ['catalogsearch_result_index', true],
            'advanced search result' => ['catalogsearch_advanced_result', true],
            '404 page' => ['cms_noroute_index', true],
            'product page' => ['catalog_product_view', false],
            'home page' => ['cms_index_index', false],
        ];
    }

    public function testBeforeRenderMetadataDoesNothingWhenModuleDisabled(): void
    {
        $this->config['enabled'] = false;
        $render = $this->buildSeoRender();
        $this->requestMock->method('getFullActionName')->willReturn('cms_noroute_index');
        $this->pageConfigMock->expects($this->never())->method('setMetadata');

        $render->beforeRenderMetadata($this->createStub(Renderer::class));
    }

    /**
     * @param array<string, mixed> $config
     */
    #[DataProvider('headContentProvider')]
    public function testAfterRenderHeadContentAppendsBlocksPerPage(
        string $action,
        array $config,
        string $expected
    ): void {
        $this->config = $config;
        $render = $this->buildPartialSeoRender([
            'getFullActionName',
            'showProductStructuredData',
            'showLogoStructureData',
            'showLocalBussinessStructureData',
            'showSiteLinksStructuredData',
        ]);
        $render->method('getFullActionName')->willReturn($action);
        $render->method('showProductStructuredData')->willReturn('[product]');
        $render->method('showLogoStructureData')->willReturn('[logo]');
        $render->method('showLocalBussinessStructureData')->willReturn('[local]');
        $render->method('showSiteLinksStructuredData')->willReturn('[sitelinks]');

        $this->assertSame($expected, $render->afterRenderHeadContent($this->createStub(Renderer::class), 'HEAD'));
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public static function headContentProvider(): array
    {
        return [
            'product page, product snippet on' => [
                'catalog_product_view', ['rich' => ['enable_product' => '1']], 'HEAD[product]'
            ],
            'product page, product snippet off' => [
                'catalog_product_view', ['rich' => ['enable_product' => '0']], 'HEAD'
            ],
            'home, business info on, sitelinks off' => [
                'cms_index_index', ['info' => ['enable' => '1']], 'HEAD[logo][local]'
            ],
            'home, business info off, sitelinks on' => [
                'cms_index_index', ['rich' => ['enable_site_link' => '1']], 'HEAD[sitelinks]'
            ],
            'home, both on' => [
                'cms_index_index',
                ['info' => ['enable' => '1'], 'rich' => ['enable_site_link' => '1']],
                'HEAD[logo][local][sitelinks]'
            ],
            'home, both off' => ['cms_index_index', [], 'HEAD'],
            'category page ignores all snippets' => [
                'catalog_category_view',
                ['info' => ['enable' => '1'], 'rich' => ['enable_product' => '1', 'enable_site_link' => '1']],
                'HEAD'
            ],
            'module disabled' => [
                'catalog_product_view', ['enabled' => false, 'rich' => ['enable_product' => '1']], 'HEAD'
            ],
        ];
    }

    #[DataProvider('socialProfilesProvider')]
    public function testGetSocialProfilesCollectsNetworksThenCustomLinks(array $social, array $expected): void
    {
        $this->config['social'] = $social;

        $this->assertSame($expected, $this->buildSeoRender()->getSocialProfiles());
    }

    /**
     * @return array<string, array{0: array<string, ?string>, 1: string[]}>
     */
    public static function socialProfilesProvider(): array
    {
        return [
            'nothing configured' => [[], []],
            'networks keep fixed order' => [
                ['linkedin' => 'https://linkedin.com/x', 'facebook' => 'https://fb.com/x', 'tiktok' => 'https://tiktok.com/x'],
                ['https://fb.com/x', 'https://tiktok.com/x', 'https://linkedin.com/x']
            ],
            'custom links are split on newline and trimmed' => [
                ['custom_link' => "  https://a.test \n https://b.test  "],
                ['https://a.test', 'https://b.test']
            ],
            'networks then custom links' => [
                ['twitter' => 'https://x.com/s', 'youtube' => 'https://yt.com/s', 'custom_link' => 'https://c.test'],
                ['https://x.com/s', 'https://yt.com/s', 'https://c.test']
            ],
        ];
    }

    public function testShowLogoStructureDataBuildsOrganizationWithOnlyFilledContactPoints(): void
    {
        $this->config['info'] = [
            'business_name' => 'Acme',
            'customer_service_phone' => '+1-800-111',
            'customer_service_contact_option' => 'TollFree',
            'technical_support_area_serve' => 'US',
        ];
        $this->config['social'] = ['facebook' => 'https://fb.com/acme'];
        $render = $this->buildSeoRender();
        $this->urlBuilderMock->method('getUrl')->willReturn('https://shop.test/');

        $html = $render->showLogoStructureData();

        $this->assertStringStartsWith('<!-- Logo Structured Data by Mageplaza SEO-->', $html);
        $data = $this->extractJson($html);
        $this->assertSame('http://schema.org/', $data['@context']);
        $this->assertSame('Organization', $data['@type']);
        $this->assertSame('https://shop.test/', $data['url']);
        $this->assertSame('https://shop.test/logo.png', $data['logo']);
        $this->assertSame('Acme', $data['name']);
        $this->assertSame(['https://fb.com/acme'], $data['sameAs']);
        $this->assertCount(2, $data['contactPoint']);
        $this->assertSame('customer service', $data['contactPoint'][0]['contactType']);
        $this->assertSame('+1-800-111', $data['contactPoint'][0]['telephone']);
        $this->assertSame('TollFree', $data['contactPoint'][0]['contactOption']);
        $this->assertSame('technical support', $data['contactPoint'][1]['contactType']);
        $this->assertSame('US', $data['contactPoint'][1]['areaServed']);
    }

    public function testShowLogoStructureDataOmitsSameAsWhenNoProfileAndKeepsSalesContact(): void
    {
        $this->config['info'] = ['business_name' => 'Acme', 'sales_phone' => '555'];
        $render = $this->buildSeoRender();

        $data = $this->extractJson($render->showLogoStructureData());

        $this->assertArrayNotHasKey('sameAs', $data);
        $this->assertCount(1, $data['contactPoint']);
        $this->assertSame('sales', $data['contactPoint'][0]['contactType']);
        $this->assertSame('555', $data['contactPoint'][0]['telephone']);
    }

    public function testShowLocalBussinessStructureDataMergesConfiguredImageUrlsAndUploadedImage(): void
    {
        $this->config['info'] = [
            'business_type' => 'Store',
            'business_name' => 'Acme',
            'street_address' => '1 Main St',
            'city' => 'Springfield',
            'state_province' => 'IL',
            'zip_code' => '62701',
            'email' => 'hi@acme.test',
            'fax' => '123',
            'customer_service_phone' => '+1-800-111',
            'price_range' => '$$',
            'description' => 'Best shop',
            'image_url' => " https://cdn.test/a.jpg \nhttps://cdn.test/b.jpg",
            'image' => 'default/logo.png',
        ];
        $this->config['general'] = ['general/country/default' => 'US'];
        $render = $this->buildSeoRender();
        $store = $this->createMock(Store::class);
        $store->method('getBaseUrl')->with(UrlInterface::URL_TYPE_MEDIA)->willReturn('https://shop.test/media/');
        $this->storeManagerMock->method('getStore')->willReturn($store);

        $data = $this->extractJson($render->showLocalBussinessStructureData());

        $this->assertSame('Store', $data['@type']);
        $this->assertSame('Acme', $data['name']);
        $this->assertSame('+1-800-111', $data['telephone']);
        $this->assertSame('$$', $data['priceRange']);
        $this->assertSame('Best shop', $data['description']);
        $this->assertSame(
            [
                '@type' => 'PostalAddress',
                'streetAddress' => '1 Main St',
                'addressLocality' => 'Springfield',
                'addressRegion' => 'IL',
                'addressCountry' => 'US',
                'postalCode' => '62701',
                'email' => 'hi@acme.test',
                'faxNumber' => '123',
            ],
            $data['address']
        );
        $this->assertSame(
            ['https://cdn.test/a.jpg', 'https://cdn.test/b.jpg', 'https://shop.test/media/mageplaza/seo/default/logo.png'],
            $data['image']
        );
    }

    public function testShowLocalBussinessStructureDataHasEmptyImageListWithoutImages(): void
    {
        $this->config['info'] = ['business_name' => 'Acme'];

        $data = $this->extractJson($this->buildSeoRender()->showLocalBussinessStructureData());

        $this->assertSame([], $data['image']);
    }

    public function testShowSiteLinksStructuredDataBuildsSearchAction(): void
    {
        $render = $this->buildSeoRender();
        $this->urlBuilderMock->method('getBaseUrl')->willReturn('https://shop.test/');
        $this->searchHelperMock->method('getResultUrl')->willReturn('https://shop.test/catalogsearch/result/');

        $html = $render->showSiteLinksStructuredData();

        $this->assertStringStartsWith('<!-- Sitelinks Searchbox Structured Data by Mageplaza SEO-->', $html);
        $this->assertSame(
            [
                '@context' => 'http://schema.org',
                '@type' => 'WebSite',
                'url' => 'https://shop.test/',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => 'https://shop.test/catalogsearch/result/?q={searchbox_target}',
                    'query-input' => 'required name=searchbox_target',
                ],
            ],
            $this->extractJson($html)
        );
    }

    #[DataProvider('dayOfWeeksProvider')]
    public function testGetDayOfWeeks(mixed $configured, array $expected): void
    {
        $this->config['shipping'] = ['business_days' => $configured];

        $this->assertSame($expected, $this->buildSeoRender()->getDayOfWeeks());
    }

    /**
     * @return array<string, array{0: mixed, 1: string[]}>
     */
    public static function dayOfWeeksProvider(): array
    {
        return [
            'null' => [null, []],
            'empty string' => ['', []],
            'one day' => ['Monday', ['https://schema.org/Monday']],
            'several days with spaces' => [
                'Monday, Wednesday ,Friday',
                ['https://schema.org/Monday', 'https://schema.org/Wednesday', 'https://schema.org/Friday']
            ],
        ];
    }

    public function testCreateShippingDetailsDataUsesConfiguredFeeWhenFreeShippingDisabled(): void
    {
        $this->config['shipping'] = [
            'enable_free_shipping' => '0',
            'free_shipping_attribute' => 'is_free',
            'business_days' => 'Monday,Tuesday',
            'cutoff_time' => '14:00:00',
            'min_handling_time' => '0',
            'max_handling_time' => '1',
            'min_transit_time' => '2',
            'max_transit_time' => '5',
            'shipping_country' => 'US',
            'shipping_fee' => '4.99',
            'shipping_currency' => 'USD',
        ];

        $data = $this->buildSeoRender()->createShippingDetailsData(new DataObject(['is_free' => 1]));

        $this->assertSame('OfferShippingDetails', $data['@type']);
        $this->assertSame(
            ['https://schema.org/Monday', 'https://schema.org/Tuesday'],
            $data['deliveryTime']['businessDays']['dayOfWeek']
        );
        $this->assertSame('14:00:00', $data['deliveryTime']['cutoffTime']);
        $this->assertSame(
            ['@type' => 'QuantitativeValue', 'minValue' => '0', 'maxValue' => '1', 'unitCode' => 'd'],
            $data['deliveryTime']['handlingTime']
        );
        $this->assertSame(
            ['@type' => 'QuantitativeValue', 'minValue' => '2', 'maxValue' => '5', 'unitCode' => 'd'],
            $data['deliveryTime']['transitTime']
        );
        $this->assertSame(['@type' => 'DefinedRegion', 'addressCountry' => 'US'], $data['shippingDestination']);
        $this->assertSame(
            ['@type' => 'MonetaryAmount', 'value' => '4.99', 'currency' => 'USD'],
            $data['shippingRate']
        );
    }

    #[DataProvider('freeShippingProvider')]
    public function testCreateShippingDetailsDataRateDependsOnFreeShippingAttribute(
        mixed $attributeValue,
        mixed $expectedRate
    ): void {
        $this->config['shipping'] = [
            'enable_free_shipping' => '1',
            'free_shipping_attribute' => 'is_free',
            'shipping_fee' => '4.99',
        ];

        $data = $this->buildSeoRender()->createShippingDetailsData(new DataObject(['is_free' => $attributeValue]));

        $this->assertSame($expectedRate, $data['shippingRate']['value']);
    }

    /**
     * @return array<string, array{0: mixed, 1: mixed}>
     */
    public static function freeShippingProvider(): array
    {
        return [
            'attribute yes' => ['1', 0],
            'attribute no' => ['0', '4.99'],
            'attribute missing' => [null, '4.99'],
        ];
    }

    public function testCreateMerchantReturnPolicyPrefixesEnumValuesWithSchemaUrl(): void
    {
        $this->config['return'] = [
            'applicable_country' => 'US',
            'return_policy_category' => 'MerchantReturnFiniteReturnWindow',
            'refund_type' => 'FullRefund',
            'return_fees' => 'FreeReturn',
            'return_method' => 'ReturnByMail',
            'return_label_source' => 'ReturnLabelDownloadAndPrint',
        ];

        $this->assertSame(
            [
                '@type' => 'MerchantReturnPolicy',
                'applicableCountry' => 'US',
                'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                'refundType' => 'https://schema.org/FullRefund',
                'returnFees' => 'https://schema.org/FreeReturn',
                'returnMethod' => 'https://schema.org/ReturnByMail',
                'returnLabelSource' => 'https://schema.org/ReturnLabelDownloadAndPrint',
            ],
            $this->buildSeoRender()->createMerchantReturnPolicy()
        );
    }

    public function testGetProductAndMetaTitleReadFromRegistry(): void
    {
        $product = new DataObject();
        $render = $this->buildSeoRender();
        $this->registryMock->method('registry')->willReturnMap([
            ['current_product', $product],
            ['meta_title', 'Custom title'],
        ]);

        $this->assertSame($product, $render->getProduct());
        $this->assertSame('Custom title', $render->getMetaTitle());
    }

    public function testGetUrlForwardsRouteAndParamsToUrlBuilder(): void
    {
        $render = $this->buildSeoRender();
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('catalog/product/view', ['id' => 5])
            ->willReturn('https://shop.test/catalog/product/view/id/5');

        $this->assertSame(
            'https://shop.test/catalog/product/view/id/5',
            $render->getUrl('catalog/product/view', ['id' => 5])
        );
    }
}
