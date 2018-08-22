<?php
/**
 * Default Config Provider
 *
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace UAE\QuoteCustom\Plugin\Model;

use Magento\Checkout\Model\DefaultConfigProvider as CheckoutDefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use UAE\QuoteCustom\Model\CartTotalsRetriever;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Cart\Totals\Item;
use Magento\Quote\Model\Cart\TotalSegment;

/**
 * UAE\QuoteCustom\Plugin\Model\DefaultConfigProvider
 *
 * @category UAE
 * @package UAE_QuoteCustom
 */
class DefaultConfigProvider
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Shipping method converter
     *
     * @var ShippingMethodConverter
     */
    private $converter;

    /**
     * @var CartTotalsRetriever
     */
    private $cartTotalsRetriever;

    /**
     * DefaultConfigProvider constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param OrderInterface $order
     * @param CartRepositoryInterface $quoteRepository
     * @param ShippingMethodConverter $converter
     * @param CartTotalsRetriever $cartTotalsRetriever
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        OrderInterface $order,
        CartRepositoryInterface $quoteRepository,
        ShippingMethodConverter $converter,
        CartTotalsRetriever $cartTotalsRetriever
    ){
        $this->checkoutSession = $checkoutSession;
        $this->order = $order;
        $this->quoteRepository = $quoteRepository;
        $this->converter = $converter;
        $this->cartTotalsRetriever = $cartTotalsRetriever;
    }

    /**
     * After get config
     *
     * @param CheckoutDefaultConfigProvider $subject
     * @param array $result
     *
     * @return array $result
     */
    public function afterGetConfig(
        CheckoutDefaultConfigProvider $subject,
        array $result
    ){
        $selectedShippingMethod = $result['selectedShippingMethod'];
        $quote = $this->checkoutSession->getQuote();

        if ($quote->getOriginatingQuoteId()) {
            try {
                $originatingQuoteId = (int) $quote->getOriginatingQuoteId();
                $order = $this->order->loadByIncrementId($originatingQuoteId);

                if ($selectedShippingMethod === null && $this->getshippingMethod($order->getQuoteId())) {
                    $shippingMethod = $this->getshippingMethod($order->getQuoteId());
                    $selectedShippingMethod = $shippingMethod->__toArray();
                }

                if (count($quote->getAllItems()) === count($order->getAllItems())) {
                    $result['totalsData'] = $this->getTotalsData($order->getQuoteId());
                }
            } catch (\Exception $exception) {
                $selectedShippingMethod = null;
            }
            $result['selectedShippingMethod'] = $selectedShippingMethod;
        }

        return $result;
    }

    /**
     * Retrieve shipping method data
     *
     * @param $quoteId
     * @return ShippingMethodInterface|null
     * @throws StateException
     */
    private function getShippingMethod($quoteId)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->get($quoteId);

        /** @var Address $shippingAddress */
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            throw new StateException(__('Shipping address not set.'));
        }

        $shippingMethod = $shippingAddress->getShippingMethod();
        if (!$shippingMethod) {
            return null;
        }

        $shippingAddress->collectShippingRates();
        /** @var Rate $shippingRate */
        $shippingRate = $shippingAddress->getShippingRateByCode($shippingMethod);
        if (!$shippingRate) {
            return null;
        }
        return $this->converter->modelToDataObject($shippingRate, $quote->getQuoteCurrencyCode());
    }

    /**
     * Return quote totals data
     *
     * @param int $cartId
     *
     * @return array
     */
    private function getTotalsData($cartId)
    {
        /** @var TotalsInterface $totals */
        $totals = $this->cartTotalsRetriever->getCartTotal($cartId);
        $items = [];
        /** @var  Item $item */
        foreach ($totals->getItems() as $item) {
            $items[] = $item->__toArray();
        }
        $totalSegmentsData = [];
        /** @var TotalSegment $totalSegment */
        foreach ($totals->getTotalSegments() as $totalSegment) {
            $totalSegmentArray = $totalSegment->toArray();
            if (is_object($totalSegment->getExtensionAttributes())) {
                $totalSegmentArray['extension_attributes'] = $totalSegment->getExtensionAttributes()->__toArray();
            }
            $totalSegmentsData[] = $totalSegmentArray;
        }
        $totals->setItems($items);
        $totals->setTotalSegments($totalSegmentsData);
        $totalsArray = $totals->toArray();
        if (is_object($totals->getExtensionAttributes())) {
            $totalsArray['extension_attributes'] = $totals->getExtensionAttributes()->__toArray();
        }

        return $totalsArray;
    }
}
