<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Plugin\GoogleAdwords\Observer;

class SetAdwordsOnOrderSuccessPageView
{
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     */
    public function __construct(\Magento\Sales\Model\OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \Magento\GoogleAdwords\Observer\SetConversionValueObserver $subject
     * @param \Magento\Framework\Event\Observer $observer
     * @return array
     */
    public function beforeExecute(
        \Magento\GoogleAdwords\Observer\SetConversionValueObserver $subject,
        \Magento\Framework\Event\Observer $observer
    ) {
        $orderIds = $observer->getEvent()->getOrderIds();

        if (!empty($orderIds) && is_array($orderIds)) {
            $actualOrderIds = [];
            foreach ($orderIds as $orderId) {
                if (!$this->orderRepository->get($orderId)->getIsQuoteRequest()) {
                    $actualOrderIds[] = $orderId;
                }
            }

            $observer->getEvent()->setOrderIds($actualOrderIds);
        }

        return [$observer];
    }
}
