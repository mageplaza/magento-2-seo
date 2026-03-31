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
 * @package     Mageplaza_DemoTutorial
 *
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DemoTutorial\Block\Adminhtml\Steps\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Cms\Model\Wysiwyg\Config;

/**
 * Class Information
 * @package Mageplaza\DemoTutorial\Block\Adminhtml\Steps\Edit\Tab
 */
class Information extends Generic implements TabInterface
{
    protected $_wysiwygConfig;

    /**
     * Information constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     */
    protected function _prepareForm()
    {
        /** @var Form $form */
        $form = $this->_formFactory->create();
        $model = $this->_coreRegistry->registry('current_step');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Step Details')]);

        if ($model->getId()) {
            $fieldset->addField('step_id', 'hidden', [
                'name'  => 'step_id',
            ]);
        }

        $fieldset->addField('module_name', 'text', [
            'name'     => 'module_name',
            'label'    => __('Title'),
            'title'    => __('Title'),
            'required' => false,
        ]);

        $fieldset->addField('page', 'text', [
            'name'     => 'page',
            'label'    => __('Page'),
            'title'    => __('Page'),
            'required' => true,
        ]);

        $fieldset->addField('element', 'text', [
            'name'     => 'element',
            'label'    => __('Element Selector'),
            'title'    => __('Element Selector'),
            'required' => true,
        ]);

        $fieldset->addField('content', 'editor', [
            'name'      => 'content',
            'label'     => __('Notice Content'),
            'title'     => __('Content'),
            'required'  => true,
            'config'    => $this->_wysiwygConfig->getConfig()
        ]);

        $fieldset->addField(
            'notice_position',
            'select',
            [
                'name' => 'notice_position',
                'label' => __('Notice Position'),
                'title' => __('Notice Position'),
                'required' => true,
                'options' => [
                    'bottom' => __('Bottom'),
                    'top' => __('Top'),
                    'right' => __('Right'),
                    'left' => __('Left')
                ]
            ]
        );

        $fieldset->addField('area', 'select', [
            'name'     => 'area',
            'label'    => __('Area'),
            'title'    => __('Area'),
            'required' => true,
            'values'   => [
                ['value' => 'adminhtml', 'label' => __('Admin')],
                ['value' => 'frontend', 'label' => __('Frontend')]
            ],
        ]);

        $fieldset->addField('is_login', 'select', [
            'name'   => 'is_login',
            'label'  => __('Require Login'),
            'title'  => __('Require Login'),
            'values' => [
                ['value' => '0', 'label' => __('No')],
                ['value' => '1', 'label' => __('Yes')]
            ],
            'required' => false,
            'note'     => __('Only used when area = frontend')
        ])->setAfterElementHtml("
            <script type=\"text/javascript\">
                require(['jquery', 'domReady!'], function($) {
                    let areaField = $('[name=\"area\"]');
                    let isLoginField = $('[name=\"is_login\"]').closest('tr');

                    function toggleIsLoginField() {
                        if (areaField.val() === 'frontend') {
                            isLoginField.show();
                        } else {
                            isLoginField.hide();
                        }
                    }

                    toggleIsLoginField();
                    areaField.on('change', toggleIsLoginField);
                });
            </script>
        ");

        $fieldset->addField('trigger', 'text', [
            'name'  => 'trigger',
            'label' => __('Trigger Condition'),
            'title' => __('Trigger Condition'),
            'required' => false
        ]);

        $fieldset->addField('position', 'text', [
            'name'  => 'position',
            'label' => __('Step Order'),
            'title' => __('Step Order'),
            'class' => 'validate-number validate-zero-or-greater',
            'value' => '0',
        ]);

        if ($model->getId()) {
            $form->setValues($model->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     */
    public function getTabLabel()
    {
        return __('Step Details');
    }

    /**
     * Prepare title for tab
     */
    public function getTabTitle()
    {
        return __('Step Details');
    }

    /**
     * Show tab
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Hide tab
     */
    public function isHidden()
    {
        return false;
    }
}
