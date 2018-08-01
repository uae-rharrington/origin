<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Controller\Order;

class View extends \Magento\Framework\App\Action\Action implements \Magento\Sales\Controller\OrderInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface
     */
    private $orderAuthorization;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface $orderAuthorization
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface $orderAuthorization
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->orderFactory = $orderFactory;
        $this->registry = $registry;
        $this->orderAuthorization = $orderAuthorization;

        parent::__construct($context);
    }

    /**
     * Order view page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->loadOrder()) {
            $this->messageManager->addErrorMessage(__('Could not load quote request.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account');
            return $resultRedirect;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('quoterequest/order/history');
        }

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function loadOrder()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');
        if (!$orderId) {
            return false;
        } else {
            $order = $this->orderFactory->create()->load($orderId);
            if (!$order) {
                return false;
            }

            if ($this->orderAuthorization->canView($order)) {
                $this->registry->register('current_order', $order);
                return true;
            }

            return false;
        }
    }
}
