<?php
/**
 * Default Config Provider
 *
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace UAE\QuoteCustom\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\Data\CartInterface;
use Psr\Log\LoggerInterface;

/**
 * UAE\QuoteCustom\Model\CheckoutConfigProvider
 *
 * @category UAE
 * @package UAE_QuoteCustom
 */
class CheckoutConfigProvider implements ConfigProviderInterface
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CheckoutConfigProvider constructor.
     *
     * @param HttpContext $httpContext
     * @param CheckoutSession $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param OrderInterface $order
     * @param LoggerInterface $logger
     */
    public function __construct(
        HttpContext $httpContext,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        OrderInterface $order,
        LoggerInterface $logger
    ) {
        $this->httpContext = $httpContext;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->order = $order;
        $this->logger = $logger;
    }

    /**
     * Retrieve config
     *
     * @return array $output
     */
    public function getConfig()
    {
        $output['guestShippingAddress'] = $this->getGuestShippingAddress();
        $output['customerShippingAddress'] = $this->getCustomerShippingAddress();
        return $output;
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
            try {
                $quote = $this->quoteRepository->get($quoteId);
                $guestShippingAddress = $this->fillAddressFields($quote);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
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
            try {
                $quote = $this->quoteRepository->get($quoteId);
                if ($originatingQuoteId = (int) $quote->getOriginatingQuoteId()) {
                    $order = $this->order->loadByIncrementId($originatingQuoteId);
                    $customerShippingAddress = $this->fillAddressFields($quote);
                    $customerShippingAddress['address_id'] = (int) $order->getShippingAddress()->getCustomerAddressId();
                }
            } catch  (\Exception $e) {
                $this->logger->critical($e->getMessage());
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
