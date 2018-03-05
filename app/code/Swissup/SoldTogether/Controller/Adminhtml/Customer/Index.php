<?php
namespace Swissup\SoldTogether\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Swissup_SoldTogether::soldtogether_customer';

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
        $resultPage->setActiveMenu('Swissup_SoldTogether::soldtogether_customer');
        $resultPage->addBreadcrumb(__('SoldTogether'), __('SoldTogether'));
        $resultPage->addBreadcrumb(__('Customers Who Bought This Item Also Bought'), __('Customers Who Bought This Item Also Bought'));
        $resultPage->getConfig()->getTitle()->prepend(__('Customers Who Bought This Item Also Bought'));

        return $resultPage;
    }
}
