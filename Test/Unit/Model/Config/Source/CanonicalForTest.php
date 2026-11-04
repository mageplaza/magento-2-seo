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

use Mageplaza\Seo\Model\Config\Source\CanonicalFor;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class CanonicalForTest extends TestCase
{
    public function testOptionValuesMatchCanonicalHelperContract(): void
    {
        $options = (new CanonicalFor())->toOptionArray();

        $this->assertSame(['0', '1', '2'], array_column($options, 'value'));
        $this->assertSame(
            ['--Please select--', 'Category', 'Product'],
            array_map('strval', array_column($options, 'label'))
        );
    }
}
