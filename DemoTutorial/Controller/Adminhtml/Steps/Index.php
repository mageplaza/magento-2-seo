<?php
namespace Mageplaza\DemoTutorial\Controller\Adminhtml\Steps;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Mageplaza_DemoTutorial::steps';

    protected $resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_DemoTutorial::tutorial_steps');
        $resultPage->getConfig()->getTitle()->prepend(__('Tutorial Steps'));
        return $resultPage;
    }
}
