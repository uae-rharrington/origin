<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Plugin\CatalogInventory\Observer;

class SubtractQuoteInventoryObserver
{
    /**
     * @param \Magento\CatalogInventory\Observer\SubtractQuoteInventoryObserver $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|mixed
     */
    public function aroundExecute(
        \Magento\CatalogInventory\Observer\SubtractQuoteInventoryObserver $subject,
        \Closure $proceed,
        \Magento\Framework\Event\Observer $observer
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        if ($quote->getIsQuoteRequest()) {
            // This is a quote request, don't reduce inventory
            return $this;
        } else {
            // This is not a quote request, reduce inventory as applicable
            return $proceed($observer);
        }
    }
}
