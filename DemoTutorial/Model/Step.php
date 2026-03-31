<?php
namespace Mageplaza\DemoTutorial\Model;

use Magento\Framework\Model\AbstractModel;

class Step extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Mageplaza\DemoTutorial\Model\ResourceModel\Step::class);
    }
}
