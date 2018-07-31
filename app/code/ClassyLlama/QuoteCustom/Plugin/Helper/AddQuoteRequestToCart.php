<?php
/**
 * Add Quote Request To Cart Plugin
 *
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\QuoteCustom\Plugin\Helper;

use Magento\Customer\Model\Session;
use ClassyLlama\Quote\Helper\AddQuoteRequestToCart as AddQuoteRequestToCartHelper;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * ClassyLlama\QuoteCustom\Plugin\Helper\AddQuoteRequestToCart
 *
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 */
class AddQuoteRequestToCart
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * AddQuoteRequestToCart constructor.
     *
     * @param Session $customerSession
     * @param Json $serializer
     */
    public function __construct(
        Session $customerSession,
        Json $serializer
    ) {
        $this->customerSession = $customerSession;
        $this->serializer = $serializer;
    }

    /**
     * Check for expired
     * Add Tax and Shipping
     *
     * @param AddQuoteRequestToCartHelper $subject
     * @param \Closure $proceed
     * @param OrderInterface $order
     * @param Cart $cart
     *
     * @return bool|array
     */
    public function aroundExecute(
        AddQuoteRequestToCartHelper $subject,
        \Closure $proceed,
        OrderInterface $order,
        Cart $cart
    ) {
        $results = [
            AddQuoteRequestToCartHelper::RESULTS_ERRORS_KEY => [],
            AddQuoteRequestToCartHelper::RESULTS_EXCEPTIONS_KEY => [],
            AddQuoteRequestToCartHelper::RESULTS_SUCCESSES_KEY => [],
            AddQuoteRequestToCartHelper::RESULTS_CART_CONTAINED_ITEMS_KEY => false
        ];

        if ($order && $order->hasItems() && $cart) {
            $createdAt = new \DateTime($order->getCreatedAt(), new \DateTimeZone('UTC'));
            $now = new \DateTime('now', new \DateTimeZone('UTC'));
            $dateTimeDelta = $createdAt->diff($now);
            $isStale = $dateTimeDelta->days > AddQuoteRequestToCartHelper::QUOTE_REQUEST_STALE_DAYS;
            $this->customerSession->setIsQuoteExpired($isStale);
            if ($cart->getItemsQty() > 0) {
                $results[AddQuoteRequestToCartHelper::RESULTS_CART_CONTAINED_ITEMS_KEY] = true;
            }
            $items = $order->getItemsCollection();
            $itemsByProductId = [];
            foreach ($items as $item) {
                try {
                    if (empty($item->getProduct()) || $item->getProduct()->getStatus() != Status::STATUS_ENABLED) {
                        throw new LocalizedException(
                            __('This product is no longer available.')
                        );
                    }
                    $productSku = $item->getSku();
                    if ($isStale) {
                        $item->setBasePrice($item->getBaseOriginalPrice());
                        $item->setPrice($item->getOriginalPrice());
                    }
                    $cart->addOrderItem($item);
                    $results[AddQuoteRequestToCartHelper::RESULTS_SUCCESSES_KEY][] = $item->getSku();
                    // Only add parent products since child products may have conflicting SKUs
                    // and their prices are $0
                    if (!$item->getParentItem()) {
                        $itemsByProductId[$productSku] = $item;
                    }
                } catch (LocalizedException $e) {
                    $results[AddQuoteRequestToCartHelper::RESULTS_ERRORS_KEY][$item->getSku()] = $e->getMessage();
                } catch (\Exception $e) {
                    $results[AddQuoteRequestToCartHelper::RESULTS_EXCEPTIONS_KEY][$item->getSku()] = $e;
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
            if (!$isStale) {
                $cart->getQuote()->getShippingAddress()->addData($order->getShippingAddress()->getData());
                $cart->getQuote()->getShippingAddress()
                    ->setCollectShippingRates(true)
                    ->collectShippingRates()
                    ->setShippingMethod($order->getShippingMethod())
                    ->setBaseTaxAmount($order->getBaseTaxAmount())
                    ->setTaxAmount($order->getTaxAmount());
                $cart->getQuote()->collectTotals();
            }
            $cart->save();
        }

        return $this->unsetEmptyResults($results);
    }

    /**
     * Sets Custom Price on Quote Item
     *
     * @param Item $item
     * @param float $newPrice
     * @return void
     */
    protected function setQuoteItemCustomPrice(Item $item, $newPrice)
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
            $this->unsetEmptyValues($results, AddQuoteRequestToCartHelper::RESULTS_SUCCESSES_KEY);
            $this->unsetEmptyValues($results, AddQuoteRequestToCartHelper::RESULTS_ERRORS_KEY);
            $this->unsetEmptyValues($results, AddQuoteRequestToCartHelper::RESULTS_EXCEPTIONS_KEY);

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
        return isset($results[AddQuoteRequestToCartHelper::RESULTS_SUCCESSES_KEY])
            || isset($results[AddQuoteRequestToCartHelper::RESULTS_ERRORS_KEY])
            || isset($results[AddQuoteRequestToCartHelper::RESULTS_EXCEPTIONS_KEY]);
    }
}