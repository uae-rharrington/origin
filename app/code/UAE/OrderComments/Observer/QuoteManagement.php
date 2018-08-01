<?php
/**
 * Quote Submit Before Observer
 *
 * @category UAE
 * @package UAE_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace UAE\OrderComments\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * UAE\OrderComments\Observer\QuoteManagement
 *
 * @category UAE
 * @package UAE_OrderComments
 */
class QuoteManagement implements ObserverInterface
{
    /**
     * Set order comment value from quote onto order
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        $order->setOrderComment($quote->getOrderComment());
    }
}
