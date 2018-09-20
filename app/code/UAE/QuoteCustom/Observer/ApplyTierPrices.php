<?php
/**
 * Apply Tier Prices Observer
 *
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
namespace UAE\QuoteCustom\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Apply tier prices to quote items in case customer is checking out a quote.
 */
class ApplyTierPrices implements ObserverInterface
{
    /**
     * Sets tier price to custom_price.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getCart()->getQuote();
        if ($quote->getOriginatingQuoteId()) {
            foreach ($quote->getAllVisibleItems() as $item) {
                $product = $item->getProduct();
                if ($product->getTierPrice()) {
                    $tierPrice = $product->getTierPrice($item->getQty());
                    $item->setCustomPrice($tierPrice);
                    $item->setOriginalCustomPrice($tierPrice);
                }
            }
        }
    }
}
