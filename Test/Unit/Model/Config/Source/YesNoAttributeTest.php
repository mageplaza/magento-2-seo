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

namespace Mageplaza\Seo\Test\Unit\Model\Config\Source;

use ArrayIterator;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\DataObject;
use Mageplaza\Seo\Model\Config\Source\YesNoAttribute;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class YesNoAttributeTest extends TestCase
{
    private function createFactory(array $attributes): CollectionFactory
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('frontend_input', 'boolean')
            ->willReturnSelf();
        $collection->method('getIterator')->willReturn(new ArrayIterator($attributes));
        $factory = $this->createMock(CollectionFactory::class);
        $factory->expects($this->once())->method('create')->willReturn($collection);

        return $factory;
    }

    public function testStartsWithPlaceholderThenBooleanAttributesWithCodeInLabel(): void
    {
        $model = new YesNoAttribute($this->createFactory([
            new DataObject(['frontend_label' => 'Free Shipping', 'attribute_code' => 'free_shipping']),
        ]));

        $options = $model->toOptionArray();

        $this->assertCount(2, $options);
        $this->assertSame('', $options[0]['value']);
        $this->assertSame('-- Please Select --', (string) $options[0]['label']);
        $this->assertSame(
            ['value' => 'free_shipping', 'label' => 'Free Shipping (free_shipping)'],
            $options[1]
        );
    }

    public function testOnlyPlaceholderWhenNoBooleanAttribute(): void
    {
        $options = (new YesNoAttribute($this->createFactory([])))->toOptionArray();

        $this->assertSame([''], array_column($options, 'value'));
    }
}
