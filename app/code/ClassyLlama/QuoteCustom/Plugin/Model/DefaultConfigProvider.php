<?php
/**
 * Default Config Provider
 *
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\QuoteCustom\Plugin\Model;

use Magento\Checkout\Model\DefaultConfigProvider as CoreDefaultConfigProvider;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * ClassyLlama\QuoteCustom\Plugin\Model\DefaultConfigProvider
 *
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 */
class DefaultConfigProvider
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * DefaultConfigProvider constructor.
     *
     * @param HttpContext $httpContext
     * @param CheckoutSession $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param OrderInterface $order
     */
    public function __construct(
        HttpContext $httpContext,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        OrderInterface $order
    ) {
        $this->httpContext = $httpContext;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->order = $order;
    }

    /**
     * Retrieve config
     *
     * @param CoreDefaultConfigProvider $subject
     * @param array $result
     *
     * @return array $result
     */
    public function afterGetConfig(
        CoreDefaultConfigProvider $subject,
        array $result
    ) {
        $result['guestShippingAddress'] = $this->getGuestShippingAddress();
        $result['customerShippingAddress'] = $this->getCustomerShippingAddress();
        return $result;
    }

    /**
     * Retrieve guest shipping address
     *
     * @return array | null
     */
    private function getGuestShippingAddress()
    {
        $guestShippingAddress = null;
        $quoteId = $this->checkoutSession->getQuote()->getId();

        if (!$this->isCustomerLoggedIn() && $quoteId) {
            $quote = $this->quoteRepository->get($quoteId);
            $guestShippingAddress = $this->fillAddressFields($quote);
        }

        return $guestShippingAddress;
    }

    /**
     * Retrieve customer shipping address
     *
     * @return array | null
     */
    private function getCustomerShippingAddress()
    {
        $customerShippingAddress = null;
        $quoteId = $this->checkoutSession->getQuote()->getId();

        if ($this->isCustomerLoggedIn() && $quoteId) {
            $quote = $this->quoteRepository->get($quoteId);

            if ($originatingQuoteId = (int) $quote->getOriginatingQuoteId()) {
                $order = $this->order->loadByIncrementId($originatingQuoteId);
                $customerShippingAddress = $this->fillAddressFields($quote);
                $customerShippingAddress['address_id'] = (int) $order->getShippingAddress()->getCustomerAddressId();
            }
        }

        return $customerShippingAddress;
    }

    /**
     * Fill address fields
     *
     * @param CartInterface $quote
     * @return array
     */
    private function fillAddressFields(CartInterface $quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddressData['email'] = $shippingAddress->getEmail();
        $shippingAddressData['company'] = $shippingAddress->getCompany();
        $shippingAddressData['telephone'] = $shippingAddress->getTelephone();
        $shippingAddressData['firstname'] = $shippingAddress->getFirstname();
        $shippingAddressData['lastname'] = $shippingAddress->getLastname();
        $shippingAddressData['street'] = $shippingAddress->getStreet();
        $shippingAddressData['city'] = $shippingAddress->getCity();
        $shippingAddressData['postcode'] = $shippingAddress->getPostcode();
        $shippingAddressData['country_id'] = $shippingAddress->getCountryId();
        $shippingAddressData['region'] = $shippingAddress->getRegion();
        $shippingAddressData['region_id'] = $shippingAddress->getRegionId();
        return $shippingAddressData;
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     * @codeCoverageIgnore
     */
    private function isCustomerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }
}