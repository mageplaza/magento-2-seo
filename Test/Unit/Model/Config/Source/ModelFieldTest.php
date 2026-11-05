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

use Mageplaza\Seo\Model\Config\Source\ModelField;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class ModelFieldTest extends TestCase
{
    public function testOptionsAreSchemaOrgProductIdentifiers(): void
    {
        $options = (new ModelField())->toOptionArray();

        $this->assertSame(['gtin8', 'gtin13', 'gtin14', 'mpn'], array_column($options, 'value'));
        $this->assertSame(
            [ModelField::GTIN8, ModelField::GTIN13, ModelField::GTIN14, ModelField::MPN],
            array_column($options, 'value')
        );
        $this->assertSame(
            array_column($options, 'value'),
            array_map('strval', array_column($options, 'label'))
        );
    }
}
