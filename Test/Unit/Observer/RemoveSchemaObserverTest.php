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

namespace Mageplaza\Seo\Test\Unit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\LayoutInterface;
use Mageplaza\Seo\Helper\Data as HelperData;
use Mageplaza\Seo\Observer\RemoveSchemaObserver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class RemoveSchemaObserverTest extends TestCase
{
    private HelperData|MockObject $helperMock;

    private ProcessorInterface|MockObject $updateMock;

    private Observer $observer;

    private RemoveSchemaObserver $model;

    protected function setUp(): void
    {
        $this->helperMock = $this->createMock(HelperData::class);
        $this->updateMock = $this->createMock(ProcessorInterface::class);
        $layout = $this->createMock(LayoutInterface::class);
        $layout->method('getUpdate')->willReturn($this->updateMock);
        $this->observer = new Observer(['layout' => $layout]);

        $this->model = new RemoveSchemaObserver($this->helperMock);
    }

    public function testAddsRemoveSchemaHandleWhenEnabledAndProductSnippetOn(): void
    {
        $this->helperMock->method('isEnabled')->willReturn(true);
        $this->helperMock->method('getRichsnippetsConfig')->with('enable_product')->willReturn('1');
        $this->updateMock->expects($this->once())->method('addHandle')->with('mpseo_remove_schema');

        $this->assertSame($this->model, $this->model->execute($this->observer));
    }

    #[DataProvider('noHandleProvider')]
    public function testDoesNotAddHandle(bool $enabled, mixed $productSnippet): void
    {
        $this->helperMock->method('isEnabled')->willReturn($enabled);
        $this->helperMock->method('getRichsnippetsConfig')->willReturn($productSnippet);
        $this->updateMock->expects($this->never())->method('addHandle');

        $this->assertSame($this->model, $this->model->execute($this->observer));
    }

    /**
     * @return array<string, array{0: bool, 1: mixed}>
     */
    public static function noHandleProvider(): array
    {
        return [
            'module disabled' => [false, '1'],
            'product snippet off' => [true, '0'],
            'product snippet null' => [true, null],
        ];
    }

    public function testSnippetConfigIsNotReadWhenModuleDisabled(): void
    {
        $this->helperMock->method('isEnabled')->willReturn(false);
        $this->helperMock->expects($this->never())->method('getRichsnippetsConfig');

        $this->model->execute($this->observer);
    }
}
