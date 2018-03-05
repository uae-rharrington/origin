<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Swissup_Easytabs::easytabs_product';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Swissup_Easytabs::easytabs');
        $resultPage->addBreadcrumb(__('Easy Tabs'), __('Easy Tabs'));
        $resultPage->addBreadcrumb(__('Manage Easy Tabs'), __('Manage Easy Tabs'));
        $resultPage->getConfig()->getTitle()->prepend(__('Product Page Tabs'));

        return $resultPage;
    }
}
