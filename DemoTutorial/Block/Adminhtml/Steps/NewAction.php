<?php

namespace Mageplaza\DemoTutorial\Block\Adminhtml\Steps;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;

/**
 * Class NewAction
 * @package Mageplaza\DemoTutorial\Block\Adminhtml\Steps
 */
class NewAction extends Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Mageplaza_DemoTutorial';
        $this->_controller = 'adminhtml_steps';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Step'));
        $this->buttonList->update('reset', 'label', __('Reset'));
    }

    public function getHeaderText()
    {
        return __('New Step');
    }
}
