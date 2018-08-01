<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Plugin\CatalogInventory\Observer;

class ReindexQuoteInventoryObserver
{
    /**
     * @param \Magento\CatalogInventory\Observer\ReindexQuoteInventoryObserver $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|mixed
     */
    public function aroundExecute(
        \Magento\CatalogInventory\Observer\ReindexQuoteInventoryObserver $subject,
        \Closure $proceed,
        \Magento\Framework\Event\Observer $observer
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        if ($quote->getIsQuoteRequest()) {
            // This is a quote request, don't reindex inventory
            return $this;
        } else {
            // This is not a quote request, reindex inventory as applicable
            return $proceed($observer);
        }
    }
}
