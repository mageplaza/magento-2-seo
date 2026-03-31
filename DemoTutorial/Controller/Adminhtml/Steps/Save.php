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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\DemoTutorial\Model\StepFactory;
use Exception;

class Save extends Action
{
    protected $dateTime;
    protected $stepFactory;

    public function __construct(Context $context, DateTime $dateTime, StepFactory $stepFactory)
    {
        parent::__construct($context);
        $this->dateTime    = $dateTime;
        $this->stepFactory = $stepFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $id = (int)$this->getRequest()->getParam('id');

        if ($data) {
            $step = $this->stepFactory->create();

            if ($id) {
                $step->load($id);
                if (!$step->getId()) {
                    $this->messageManager->addErrorMessage(__('This step does not exist.'));
                    return $this->_redirect('*/*/');
                }
                $data['updated_at'] = $this->dateTime->gmtDate();
            }

            try {
                $step->addData($data)->save();
                $this->messageManager->addSuccessMessage(__('The Step has been saved.'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/steps/edit', ['id' => $step->getId()]);
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the step.'));
            }
        }

        return $this->_redirect('*/*/');
    }
}
