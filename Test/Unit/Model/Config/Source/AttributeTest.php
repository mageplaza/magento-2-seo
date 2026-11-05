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
use Mageplaza\Seo\Model\Config\Source\Attribute;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class AttributeTest extends TestCase
{
    private function createFactory(array $attributes): CollectionFactory
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('addVisibleFilter')->willReturnSelf();
        $collection->method('getIterator')->willReturn(new ArrayIterator($attributes));
        $factory = $this->createMock(CollectionFactory::class);
        $factory->expects($this->once())->method('create')->willReturn($collection);

        return $factory;
    }

    public function testMapsVisibleAttributesToLabelAndCodeOptions(): void
    {
        $model = new Attribute($this->createFactory([
            new DataObject(['frontend_label' => 'Color', 'attribute_code' => 'color']),
            new DataObject(['frontend_label' => 'Brand', 'attribute_code' => 'manufacturer']),
        ]));

        $this->assertSame(
            [
                ['label' => 'Color', 'value' => 'color'],
                ['label' => 'Brand', 'value' => 'manufacturer'],
            ],
            $model->toOptionArray()
        );
    }

    public function testReturnsEmptyArrayWhenNoVisibleAttribute(): void
    {
        $this->assertSame([], (new Attribute($this->createFactory([])))->toOptionArray());
    }
}
