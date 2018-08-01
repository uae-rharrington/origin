<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Plugin\CatalogInventory\Observer;

class RevertQuoteInventoryObserver
{
    /**
     * @param \Magento\CatalogInventory\Observer\RevertQuoteInventoryObserver $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Closure|void
     */
    public function aroundExecute(
        \Magento\CatalogInventory\Observer\RevertQuoteInventoryObserver $subject,
        \Closure $proceed,
        \Magento\Framework\Event\Observer $observer
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        if ($quote->getIsQuoteRequest()) {
            // This is a quote request, don't revert inventory because it hasn't been reduced
            return;
        } else {
            // This is not a quote request, revert inventory as applicable
            return $proceed($observer);
        }
    }
}
