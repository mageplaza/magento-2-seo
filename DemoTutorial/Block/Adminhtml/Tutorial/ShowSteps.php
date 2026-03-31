<?php
namespace Mageplaza\DemoTutorial\Block\Adminhtml\Tutorial;

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

    public function getSteps($page = null, $area = null)
    {
        $collection = $this->stepCollectionFactory->create()
            ->setOrder('position', 'ASC');

        if ($page !== null) {
            $collection->addFieldToFilter('page', ['eq' => $page]);
        }

        if ($area !== null) {
            $collection->addFieldToFilter('area', ['eq' => $area]);
        }

        return $collection->getItems();
    }
}
