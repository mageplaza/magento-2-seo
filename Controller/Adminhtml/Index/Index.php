<?php

namespace Mageplaza\Seo\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

	/**
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->getResultPageFactory()->create();
		$resultPage->setActiveMenu('Mageplaza_Seo::dashboard');
		$resultPage->addBreadcrumb(__('SEO'), __('Dashboard'));
		$resultPage->getConfig()->getTitle()->prepend(__('SEO Dashboard'));

		$this->messageManager->addSuccessMessage(__('This is a success message.'));
		$this->messageManager->addNoticeMessage(__('This is a Notice message.'));
		$this->messageManager->addWarningMessage(__('This is a Warning message.'));
		$this->messageManager->addErrorMessage(__('This is a Warning message.'));


		return $resultPage;
	}

	/**
	 * @return \Magento\Framework\View\Result\PageFactory
	 */
	public function getResultPageFactory()
	{
		return $this->resultPageFactory;
	}

	/**
	 * Check for is allowed
	 *
	 * @return boolean
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Mageplaza_Seo::dashboard');
	}
}
