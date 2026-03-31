<?php
namespace Mageplaza\DemoTutorial\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Step extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('mageplaza_tutorial_step', 'step_id');
    }
}
