<?php

namespace Mageplaza\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class GenerateBlocksAfterObserver implements ObserverInterface
{

    public function execute(Observer $observer)
    {
        return $this->basicSetup($observer);
    }

    public function basicSetup($observer)
    {
        return $this;
    }


}