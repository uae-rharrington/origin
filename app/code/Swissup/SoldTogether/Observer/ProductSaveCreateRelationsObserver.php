<?php
namespace Swissup\SoldTogether\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductSaveCreateRelationsObserver implements ObserverInterface
{
    /**
     * Create order relations
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller    = $observer->getEvent()->getController();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $linksData     = $controller->getRequest()->getParam('links');

        if (!$linksData) {
            return $this;
        }
        $productId     = $controller->getRequest()->getParam('id');
        $productParams = $controller->getRequest()->getParam('product');
        $productName   = $productParams['name'];
        if (array_key_exists('sold_order', $linksData)) {
            $orderData = $linksData['sold_order'];
            $orderModel = $objectManager->get('Swissup\SoldTogether\Model\Order');
            $orderModel->updateProductRelations($orderData, $productId, $productName);
        }
        if (array_key_exists('sold_customer', $linksData)) {
            $customerData = $linksData['sold_customer'];
            $customerModel = $objectManager->get('Swissup\SoldTogether\Model\Customer');
            $customerModel->updateProductRelations($customerData, $productId, $productName);
        }

        return $this;
    }
}
