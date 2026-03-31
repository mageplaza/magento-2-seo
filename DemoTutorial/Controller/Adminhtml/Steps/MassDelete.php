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

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\DemoTutorial\Model\ResourceModel\Step\CollectionFactory;

/**
 * Class MassDelete
 * @package Mageplaza\DemoTutorial\Controller\Adminhtml\Steps
 */
class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $step;

    /**
     * MassDelete constructor.
     *
     * @param Filter $filter
     * @param Action\Context $context
     * @param CollectionFactory $step
     */
    public function __construct(
        Filter $filter,
        Action\Context $context,
        CollectionFactory $step
    ) {
        $this->filter   = $filter;
        $this->step = $step;

        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $collection = $this->filter->getCollection($this->step->create());
            $deleted    = 0;
            foreach ($collection->getItems() as $item) {
                $item->delete();
                $deleted++;
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. %1', $e->getMessage())
            );
            $this->_redirect('mptutorial/*/');

            return;
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deleted)
        );

        return $resultRedirect->setPath('mptutorial/*/');
    }
}
