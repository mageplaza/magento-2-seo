<?php

namespace Mageplaza\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Mageplaza\Seo\Helper\Data as SeoHelper;
use Magento\Framework\Registry;
use Magecheckout\Seo\Model\Source\Robots;
class LayoutRenderElementObserver implements ObserverInterface
{
    protected $helper;
    protected $registry;
    public function __construct(
        SeoHelper $helper,
        Registry $registry
    )
    {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    public function execute(Observer $observer)
    {
        $elementName=$observer->getEvent()->getElementName();
        $layout=$observer->getEvent()->getLayout();
        $transport=$observer->getEvent()->getTransport();
        $transport->setOutput($transport->getOutput());
        return $this;
    }


}