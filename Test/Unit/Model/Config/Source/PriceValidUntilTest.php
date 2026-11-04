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

use Mageplaza\Seo\Model\Config\Source\PriceValidUntil;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class PriceValidUntilTest extends TestCase
{
    public function testOptionsExposeEveryConstantOnceInOrder(): void
    {
        $options = (new PriceValidUntil())->toOptionArray();

        $this->assertSame(
            [
                PriceValidUntil::NONE,
                PriceValidUntil::PLUS_7,
                PriceValidUntil::PLUS_30,
                PriceValidUntil::PLUS_60,
                PriceValidUntil::PLUS_1_YEAR,
                PriceValidUntil::CUSTOM,
            ],
            array_column($options, 'value')
        );
        $this->assertSame(
            ['none', '7days', '30days', '60days', '1year', 'custom'],
            array_column($options, 'value')
        );
        $this->assertSame(
            ['None', '+7 days', '+30 days', '+60 days', '+1 year', 'Custom'],
            array_map('strval', array_column($options, 'label'))
        );
    }
}
