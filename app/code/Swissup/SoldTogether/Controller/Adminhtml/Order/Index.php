<?php
namespace Swissup\SoldTogether\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Swissup_SoldTogether::soldtogether_order';

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
        $resultPage->setActiveMenu('Swissup_SoldTogether::soldtogether_order');
        $resultPage->addBreadcrumb(__('SoldTogether'), __('SoldTogether'));
        $resultPage->addBreadcrumb(__('Frequently Bought Together'), __('Frequently Bought Together'));
        $resultPage->getConfig()->getTitle()->prepend(__('Frequently Bought Together'));

        return $resultPage;
    }
}
