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
     * DefaultConfigProvider constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param OrderInterface $order
     * @param CartRepositoryInterface $quoteRepository
     * @param ShippingMethodConverter $converter
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        OrderInterface $order,
        CartRepositoryInterface $quoteRepository,
        ShippingMethodConverter $converter
    ){
        $this->checkoutSession = $checkoutSession;
        $this->order = $order;
        $this->quoteRepository = $quoteRepository;
        $this->converter = $converter;
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

        if ($selectedShippingMethod === null && $quote->getOriginatingQuoteId()) {
            $shippingMethodData = null;
            try {
                $originatingQuoteId = (int) $quote->getOriginatingQuoteId();
                $order = $this->order->loadByIncrementId($originatingQuoteId);
                $shippingMethod = $this->getshippingMethod($order->getQuoteId());
                if ($shippingMethod) {
                    $shippingMethodData = $shippingMethod->__toArray();
                }
            } catch (\Exception $exception) {
                $shippingMethodData = null;
            }
            $result['selectedShippingMethod'] = $shippingMethodData;
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
}
