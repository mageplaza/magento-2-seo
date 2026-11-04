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

namespace Mageplaza\Seo\Test\Unit\Stub;

use Magento\Framework\DataObject;

class ProductStub extends DataObject
{
    public function __construct(
        array $data = [],
        private ?object $priceInfo = null,
        private ?object $typeInstance = null,
        private ?object $resource = null,
        private ?object $store = null,
        private bool $available = true
    ) {
        parent::__construct($data);
    }

    public function getPriceInfo(): ?object
    {
        return $this->priceInfo;
    }

    public function getTypeInstance(): ?object
    {
        return $this->typeInstance;
    }

    public function getResource(): ?object
    {
        return $this->resource;
    }

    public function getStore(): ?object
    {
        return $this->store;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function load(mixed $id): self
    {
        return $this;
    }
}
