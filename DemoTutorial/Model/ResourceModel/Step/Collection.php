<?php
namespace Mageplaza\DemoTutorial\Model\ResourceModel\Step;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageplaza\DemoTutorial\Model\Step;
use Mageplaza\DemoTutorial\Model\ResourceModel\Step as StepResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Step::class, StepResource::class);
    }
}
