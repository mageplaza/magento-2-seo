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

use Magento\Framework\App\Request\Http;
use Magento\Review\Block\Product\ReviewRenderer;
use Mageplaza\Seo\Helper\Data as HelperData;
use Mageplaza\Seo\Plugin\RemoveReview;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class RemoveReviewTest extends TestCase
{
    private const HTML = '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">'
        . '<span itemprop="ratingValue">80</span><meta itemprop="bestRating" content="100">'
        . '<span itemprop="reviewCount">3</span></div>';

    private HelperData|MockObject $helperMock;

    private RemoveReview $plugin;

    protected function setUp(): void
    {
        $this->helperMock = $this->createMock(HelperData::class);
        $this->plugin = new RemoveReview($this->helperMock);
    }

    private function createSubject(string $actionName): ReviewRenderer|MockObject
    {
        $request = $this->createMock(Http::class);
        $request->method('getFullActionName')->willReturn($actionName);
        $subject = $this->createMock(ReviewRenderer::class);
        $subject->method('getRequest')->willReturn($request);

        return $subject;
    }

    public function testStripsMicrodataOnProductViewWhenProductSnippetEnabled(): void
    {
        $this->helperMock->method('isEnabled')->willReturn(true);
        $this->helperMock->method('getRichsnippetsConfig')->with('enable_product')->willReturn('1');

        $result = $this->plugin->afterGetReviewsSummaryHtml(
            $this->createSubject('catalog_product_view'),
            self::HTML
        );

        foreach (['aggregateRating', 'itemscope', 'schema.org', 'ratingValue', 'bestRating', 'reviewCount'] as $token) {
            $this->assertStringNotContainsString($token, $result);
        }
        $this->assertStringContainsString('>80</span>', $result);
        $this->assertStringContainsString('>3</span>', $result);
    }

    #[DataProvider('untouchedProvider')]
    public function testLeavesHtmlUntouched(bool $enabled, mixed $productSnippet, string $action): void
    {
        $this->helperMock->method('isEnabled')->willReturn($enabled);
        $this->helperMock->method('getRichsnippetsConfig')->willReturn($productSnippet);

        $result = $this->plugin->afterGetReviewsSummaryHtml($this->createSubject($action), self::HTML);

        $this->assertSame(self::HTML, $result);
    }

    /**
     * @return array<string, array{0: bool, 1: mixed, 2: string}>
     */
    public static function untouchedProvider(): array
    {
        return [
            'module disabled' => [false, '1', 'catalog_product_view'],
            'product snippet off' => [true, '0', 'catalog_product_view'],
            'category page' => [true, '1', 'catalog_category_view'],
        ];
    }
}
