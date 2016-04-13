<?php

namespace Mageplaza\Seo\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Seo\Helper\Data as HelperData;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;
class Seo extends Template
{
    protected $storeManager;
    protected $helperData;
    protected $objectFactory;
    protected $checkoutSession;

    public function __construct(
        Context $context,
        HelperData $helperData,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Session $session,
        array $data = []
    ) {
        $this->helperData      = $helperData;
        $this->objectManager   = $objectManager;
        $this->storeManager    = $storeManager;
        $this->checkoutSession = $session;
        parent::__construct($context, $data);
    }
    public function getHelper()
    {
        return $this->helperData;
    }

}