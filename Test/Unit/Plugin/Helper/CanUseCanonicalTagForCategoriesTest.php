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

namespace Mageplaza\Seo\Test\Unit\Plugin\Helper;

use Magento\Catalog\Helper\Category;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Seo\Helper\Data as HelperData;
use Mageplaza\Seo\Plugin\Helper\CanUseCanonicalTagForCategories;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class CanUseCanonicalTagForCategoriesTest extends TestCase
{
    #[DataProvider('resultProvider')]
    public function testOverridesResultWithSeoConfigForCurrentStore(
        bool $enabled,
        bool $originalResult,
        string $duplicateConfig,
        bool $expected
    ): void {
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn(4);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $helper = $this->getMockBuilder(HelperData::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isEnabled', 'getDuplicateConfig'])
            ->getMock();
        $helper->method('isEnabled')->willReturn($enabled);
        $helper->method('getDuplicateConfig')->with('canonical_tag', 4)->willReturn($duplicateConfig);

        $plugin = new CanUseCanonicalTagForCategories($storeManager, $helper);

        $this->assertSame(
            $expected,
            $plugin->afterCanUseCanonicalTag($this->createMock(Category::class), $originalResult)
        );
    }

    /**
     * @return array<string, array{0: bool, 1: bool, 2: string, 3: bool}>
     */
    public static function resultProvider(): array
    {
        return [
            'enabled, category selected, magento off' => [true, false, '1', true],
            'enabled, category not selected, magento on' => [true, true, '2', false],
            'enabled, both selected' => [true, false, '1,2', true],
            'disabled keeps magento true' => [false, true, '0', true],
            'disabled keeps magento false' => [false, false, '1', false],
        ];
    }
}
