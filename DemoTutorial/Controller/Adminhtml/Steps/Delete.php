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

namespace Mageplaza\DemoTutorial\Controller\Adminhtml\Steps;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mageplaza\DemoTutorial\Model\StepFactory;
use Exception;

class Delete extends Action
{
    protected $stepFactory;

    public function __construct(Context $context, StepFactory $stepFactory)
    {
        parent::__construct($context);
        $this->stepFactory = $stepFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $step = $this->stepFactory->create();
            $step->load($id);

            if (!$step->getId()) {
                $this->messageManager->addErrorMessage(__('This step does not exist.'));
                return $this->_redirect('*/*/');
            }

            try {
                $step->delete();
                $this->messageManager->addSuccessMessage(__('The Step has been deleted.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleting the step.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Step to delete was not found.'));
        }

        return $this->_redirect('*/*/');
    }
}
