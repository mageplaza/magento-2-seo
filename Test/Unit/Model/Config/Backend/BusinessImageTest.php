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

namespace Mageplaza\Seo\Test\Unit\Model\Config\Backend;

use Magento\Framework\Filesystem\Directory\WriteInterface;
use Mageplaza\Seo\Model\Config\Backend\BusinessImage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

#[AllowMockObjectsWithoutExpectations]
class BusinessImageTest extends TestCase
{
    private function createModel(string $scope, ?int $scopeId): BusinessImage
    {
        $model = $this->getMockBuilder(BusinessImage::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $model->setScope($scope);
        $model->setScopeId($scopeId);

        $directory = $this->createStub(WriteInterface::class);
        $directory->method('getAbsolutePath')->willReturnCallback(
            static fn (string $path): string => '/var/www/pub/media/' . $path
        );
        (new ReflectionProperty(BusinessImage::class, '_mediaDirectory'))->setValue($model, $directory);

        return $model;
    }

    private function call(BusinessImage $model, string $method): mixed
    {
        return (new ReflectionMethod(BusinessImage::class, $method))->invoke($model);
    }

    public function testAllowedExtensionsAreWebImageFormatsOnly(): void
    {
        $this->assertSame(['jpg', 'jpeg', 'png', 'svg'], $this->call($this->createModel('default', 0), '_getAllowedExtensions'));
    }

    public function testScopeInfoIsAlwaysAppended(): void
    {
        $this->assertTrue($this->call($this->createModel('default', 0), '_addWhetherScopeInfo'));
    }

    #[DataProvider('uploadDirProvider')]
    public function testUploadDirIsUnderMediaMageplazaSeoWithScope(string $scope, ?int $scopeId, string $expected): void
    {
        $this->assertSame($expected, $this->call($this->createModel($scope, $scopeId), '_getUploadDir'));
    }

    /**
     * @return array<string, array{0: string, 1: ?int, 2: string}>
     */
    public static function uploadDirProvider(): array
    {
        return [
            'default scope' => ['default', 0, '/var/www/pub/media/mageplaza/seo/default'],
            'website scope' => ['websites', 2, '/var/www/pub/media/mageplaza/seo/websites/2'],
            'store scope' => ['stores', 5, '/var/www/pub/media/mageplaza/seo/stores/5'],
        ];
    }
}
