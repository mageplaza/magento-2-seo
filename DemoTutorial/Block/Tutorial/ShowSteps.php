<?php
namespace Mageplaza\DemoTutorial\Block\Tutorial;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\DemoTutorial\Model\ResourceModel\Step\CollectionFactory;

class ShowSteps extends Template
{
    protected $stepCollectionFactory;

    public function __construct(
        Context $context,
        CollectionFactory $stepCollectionFactory,
        array $data = []
    ) {
        $this->stepCollectionFactory = $stepCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getSteps($isLogin = false, $area = null)
    {
        $collection = $this->stepCollectionFactory->create()
            ->setOrder('position', 'ASC');

        if ($area !== null) {
            $collection->addFieldToFilter('area', ['eq' => $area]);
        }

        $collection->addFieldToFilter('is_login', ['eq' => $isLogin]);

        return $collection->getItems();
    }
}
