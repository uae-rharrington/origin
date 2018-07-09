<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Observer;

class QuoteManagement implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Sets isQuoteRequest value from quote onto order
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();

        $order->setIsQuoteRequest($quote->getIsQuoteRequest());
        $order->setOriginatingQuoteId($quote->getOriginatingQuoteId());
    }
}
