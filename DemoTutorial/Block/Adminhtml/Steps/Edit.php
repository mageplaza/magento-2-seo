<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Demo
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DemoTutorial\Block\Adminhtml\Steps;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mageplaza\DemoTutorial\Model\Step;

/**
 * Class Edit
 * @package Mageplaza\DemoTutorial\Block\Adminhtml\Steps
 */
class Edit extends Container
{
    protected $_coreRegistry;

    public function __construct(Context $context, Registry $coreRegistry, array $data = [])
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Mageplaza_DemoTutorial';
        $this->_controller = 'adminhtml_steps';

        parent::_construct();

        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ],
            -100
        );

        $this->buttonList->update('save', 'label', __('Save Step'));
        $this->buttonList->update('reset', 'label', __('Reset'));
    }

    public function getHeaderText()
    {
        /** @var Step $step */
        $step = $this->_coreRegistry->registry('current_step');
        if ($step && $step->getId()) {
            return __('Edit Step "%1"', $this->escapeHtml($step->getTitle()));
        }

        return __('New Step');
    }
}
