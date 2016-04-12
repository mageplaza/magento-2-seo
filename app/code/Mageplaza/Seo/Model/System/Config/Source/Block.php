<?php

namespace Magecheckout\Maintenance\Model\System\Config\Source;

use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;

class Block implements \Magento\Framework\Option\ArrayInterface
{
    protected $blockRepository;
    protected $block;
    protected $blockFactory;

    public function __construct(
        CollectionFactory $blockFactory
    ) {
        $this->blockFactory    = $blockFactory;
    }

    public function getOptionArray()
    {
        $blocks     = array();
        $collection = $this->blockFactory->create();
        foreach ($collection as $item) {
            $blocks[] = ['value' => $item->getIdentifier(), 'label' => $item->getTitle()];
        }

        return $blocks;
    }

    public function toOptionArray()
    {
        return self::getOptionArray();
    }
}