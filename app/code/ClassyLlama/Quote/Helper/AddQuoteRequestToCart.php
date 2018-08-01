<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Helper;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class AddQuoteRequestToCart extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Number of days that a Quote Request's pricing data is valid for
     */
    const QUOTE_REQUEST_STALE_DAYS = 'checkout/cart/quote_sale_pricing';

    /**
     * Results array key for non-exceptional errors encountered during addQuoteRequestToCart
     */
    const RESULTS_ERRORS_KEY = 'errors';

    /**
     * Results array key for exceptions encountered during addQuoteRequestToCart
     */
    const RESULTS_EXCEPTIONS_KEY = 'exceptions';

    /**
     * Results array key for items successfully added during addQuoteRequestToCart
     */
    const RESULTS_SUCCESSES_KEY = 'successes';

    /**
     * Results array key for boolean pertaining to a cart previously containing items during addQuoteRequestToCart
     */
    const RESULTS_CART_CONTAINED_ITEMS_KEY = 'hadExistingItems';

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        ScopeConfigInterface $scopeConfig
    ){
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Adds Items from the given Quote Request Order to the given Cart
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Checkout\Model\Cart $cart
     * @return bool|array
     */
    public function execute(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $results = [
            self::RESULTS_ERRORS_KEY => [],
            self::RESULTS_EXCEPTIONS_KEY => [],
            self::RESULTS_SUCCESSES_KEY => [],
            self::RESULTS_CART_CONTAINED_ITEMS_KEY => false
        ];

        if ($order && $order->hasItems() && $cart) {
            $createdAt = new \DateTime($order->getCreatedAt(), new \DateTimeZone('UTC'));
            $now = new \DateTime('now', new \DateTimeZone('UTC'));
            $dateTimeDelta = $createdAt->diff($now);
            $isStale = $dateTimeDelta->days > $this->getQuoteSaleLifetime();
            if ($cart->getItemsQty() > 0) {
                $results[self::RESULTS_CART_CONTAINED_ITEMS_KEY] = true;
            }
            $items = $order->getItemsCollection();
            $itemsByProductId = [];
            foreach ($items as $item) {
                try {
                    if (empty($item->getProduct()) || $item->getProduct()->getStatus() != Status::STATUS_ENABLED) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('This product is no longer available.')
                        );
                    }
                    $productSku = $item->getSku();
                    if ($isStale) {
                        $item->setBasePrice($item->getBaseOriginalPrice());
                        $item->setPrice($item->getOriginalPrice());
                    }
                    $cart->addOrderItem($item);
                    $results[self::RESULTS_SUCCESSES_KEY][] = $item->getSku();
                    // Only add parent products since child products may have conflicting SKUs and their prices are $0
                    if (!$item->getParentItem()) {
                        $itemsByProductId[$productSku] = $item;
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $results[self::RESULTS_ERRORS_KEY][$item->getSku()] = $e->getMessage();
                } catch (\Exception $e) {
                    $results[self::RESULTS_EXCEPTIONS_KEY][$item->getSku()] = $e;
                }
            }

            if (!$isStale) {
                foreach ($cart->getQuote()->getItemsCollection() as $cartItem) {
                    $productSku = $cartItem->getSku();
                    if (isset($itemsByProductId[$productSku])) {
                        if ($cartItem->getPrice() !== $itemsByProductId[$productSku]->getPrice()) {
                            $this->setQuoteItemCustomPrice($cartItem, $itemsByProductId[$productSku]->getPrice());
                        }
                    }
                }
            }

            $cart->getQuote()->setOriginatingQuoteId($order->getIncrementId());
            $cart->save();
        }

        return $this->unsetEmptyResults($results);
    }

    /**
     * Get quote sale pricing lifetime
     *
     * @return string
     **/
    protected function getQuoteSaleLifetime()
    {
        return $this->scopeConfig->getValue(
            self::QUOTE_REQUEST_STALE_DAYS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Sets Custom Price on Quote Item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $newPrice
     * @return void
     */
    protected function setQuoteItemCustomPrice(\Magento\Quote\Model\Quote\Item $item, $newPrice)
    {
        if ($newPrice <= 0) {
            return;
        }
        $buyRequest = $item->getBuyRequest();
        if ($buyRequest) {
            $buyRequest->setCustomPrice($newPrice);
            $buyRequest->setValue($this->serializer->serialize($buyRequest->getData()));
            $buyRequest->setCode('info_buyRequest');
            $buyRequest->setProduct($item->getProduct());

            $item->addOption($buyRequest);
        }

        $item->setCustomPrice($newPrice);
        $item->setOriginalCustomPrice($newPrice);
    }

    /**
     * Removes empty results to simplify analysis of return values, if no values exist in results false is returned
     *
     * @param array $results
     * @return bool|array
     */
    protected function unsetEmptyResults(array $results)
    {
        if ($results) {
            $this->unsetEmptyValues($results, self::RESULTS_SUCCESSES_KEY);
            $this->unsetEmptyValues($results, self::RESULTS_ERRORS_KEY);
            $this->unsetEmptyValues($results, self::RESULTS_EXCEPTIONS_KEY);

            if (!$this->hasResults($results)) {
                $results = false;
            }
        }

        return $results;
    }

    /**
     * @param array $results
     * @param string $key
     * @return array
     */
    protected function unsetEmptyValues(array $results, $key)
    {
        if (isset($results[$key]) && empty($results[$key])) {
            unset($results[$key]);
        }

        return $results;
    }

    /**
     * @param array $results
     * @return bool
     */
    protected function hasResults(array $results)
    {
        return isset($results[self::RESULTS_SUCCESSES_KEY])
            || isset($results[self::RESULTS_ERRORS_KEY])
            || isset($results[self::RESULTS_EXCEPTIONS_KEY]);
    }
}
