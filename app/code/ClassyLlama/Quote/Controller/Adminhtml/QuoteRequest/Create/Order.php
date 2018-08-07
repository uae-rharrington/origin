<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Controller\Adminhtml\QuoteRequest\Create;

class Order extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this->_getSession()->clearStorage();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        if (!$this->_objectManager->get('Magento\Sales\Helper\Reorder')->canReorder($order->getEntityId())) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($order->getId()) {
            $order->setReordered(true);
            $this->_getSession()->setUseOldShippingMethod(true);
            $order->setOriginatingQuoteId($order->getIncrementId());
            $this->_getOrderCreateModel()->initFromOrder($order);
            $quote = $this->_getQuote();
            $quote->setOriginatingQuoteId($order->getIncrementId());

            $resultRedirect->setPath('sales/order_create');
        } else {
            $resultRedirect->setPath('sales/quoterequest');
        }
        return $resultRedirect;
    }
}
