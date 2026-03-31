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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\DemoTutorial\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\DemoTutorial\Model\StepFactory;

/**
 * Class Template
 * @package Mageplaza\DemoTutorial\Controller\Adminhtml
 */
abstract class Step extends \Magento\Backend\App\Action
{
    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var string[]
     */
    protected $_publicActions = ['index', 'edit'];

    /** @var Registry */
    protected $registry;

    /** @var PageFactory */
    protected $resultPageFactory;

    /** @var  StepFactory */
    protected $_stepFactory;

    /**
     * Guide constructor.
     * @param Registry $registry
     * @param Context $context
     * @param Page $resultPage
     * @param PageFactory $resultPageFactory
     * @param StepFactory $stepFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory,
        StepFactory $stepFactory
    ) {
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->_stepFactory = $stepFactory;
        parent::__construct($context);
    }

    protected function _initObject()
    {
        $id = (int)$this->getRequest()->getParam('id');

        $step = $this->_stepFactory->create();
        if ($id) {
            $step->load($id);
            if (!$step->getId()) {
                $this->messageManager->addErrorMessage(__('This guide no longer exists.'));

                return false;
            }
        }

        return $step;
    }
}
