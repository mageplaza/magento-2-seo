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


use Mageplaza\DemoTutorial\Controller\Adminhtml\Step;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\DemoTutorial\Model\StepFactory;
/**
 * Class Edit
 * @package Mageplaza\DemoTutorial\Controller\Adminhtml\Step
 */
class Edit extends Step
{

    /**
     * Step constructor.
     * @param Registry $registry
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param GuideFactory $guideFactory
     */

    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory,
        StepFactory $stepFactory
    ) {
        parent::__construct($context, $registry, $resultPageFactory, $stepFactory);
    }
    public function execute()
    {
        $step   = $this->_initObject();
        if ($step) {
            $this->registry->register('current_step', $step);
            /** @var Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend($step->getGuideId() ? __(
                "Edit Step '%1'",
                $step->getStepId()
            ) : __('Edit Step'));

            return $resultPage;
        }

        $this->_redirect('*/*/');
    }
}
