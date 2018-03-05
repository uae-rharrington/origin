<?php
namespace Swissup\SoldTogether\Observer;

use Magento\Framework\Event\ObserverInterface;

class CreateOrderRelationsObserver implements ObserverInterface
{
    /**
     * Create order relations
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $orderModel = $objectManager->get('Swissup\SoldTogether\Model\Order');
        $orderModel->createNewRelations($order);

        $customerModel = $objectManager->get('Swissup\SoldTogether\Model\Customer');
        $customerModel->createNewRelations($order);

        return $this;
    }
}
