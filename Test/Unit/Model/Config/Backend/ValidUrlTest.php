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

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mageplaza\Seo\Model\Config\Backend\ValidUrl;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class ValidUrlTest extends TestCase
{
    private ManagerInterface|MockObject $eventManagerMock;

    private ValidUrl $model;

    protected function setUp(): void
    {
        $this->eventManagerMock = $this->createMock(ManagerInterface::class);
        $context = $this->createStub(Context::class);
        $context->method('getEventDispatcher')->willReturn($this->eventManagerMock);

        $this->model = new ValidUrl(
            $context,
            $this->createStub(Registry::class),
            $this->createStub(ScopeConfigInterface::class),
            $this->createStub(TypeListInterface::class)
        );
    }

    #[DataProvider('validValueProvider')]
    public function testValidValuePassesAndSaveEventIsDispatched(?string $value): void
    {
        $this->model->setValue($value);
        $this->eventManagerMock->expects($this->atLeastOnce())
            ->method('dispatch')
            ->with($this->stringContains('save_before'));

        $this->model->beforeSave();
    }

    /**
     * @return array<string, array{0: ?string}>
     */
    public static function validValueProvider(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'single url' => ['https://www.facebook.com/page'],
            'two urls' => ["https://a.example.com\nhttp://b.example.com/path?x=1"],
            'urls with surrounding spaces and CRLF' => ["  https://a.example.com  \r\n  https://b.example.com\t"],
        ];
    }

    #[DataProvider('invalidValueProvider')]
    public function testInvalidValueThrowsValidatorExceptionAndSkipsSave(string $value): void
    {
        $this->model->setValue($value);
        $this->eventManagerMock->expects($this->never())->method('dispatch');

        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Invalid url format.');

        $this->model->beforeSave();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidValueProvider(): array
    {
        return [
            'plain text' => ['not a url'],
            'missing scheme' => ['www.example.com'],
            'second line invalid' => ["https://a.example.com\nbroken"],
            'blank line between urls' => ["https://a.example.com\n\nhttps://b.example.com"],
        ];
    }
}
