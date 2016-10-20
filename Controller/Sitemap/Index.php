<?php
namespace Mageplaza\Seo\Controller\Sitemap;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $pageFactory;

    public function __construct(Context $context, PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;

        return parent::__construct($context);
    }

    public function execute()
    {
        $page = $this->pageFactory->create();;
        $page->getConfig()->getTitle()->set(__('HTML Sitemap'));

        return $page;
    }
}