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
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\Data\CartInterface;
use ClassyLlama\Quote\Helper\Data;

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
     * @var OrderInterface
     */
    private $order;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * CheckoutConfigProvider constructor.
     *
     * @param HttpContext $httpContext
     * @param CheckoutSession $checkoutSession
     * @param OrderInterface $order
     * @param Data $helperData
     */
    public function __construct(
        HttpContext $httpContext,
        CheckoutSession $checkoutSession,
        OrderInterface $order,
        Data $helperData
    ) {
        $this->httpContext = $httpContext;
        $this->checkoutSession = $checkoutSession;
        $this->order = $order;
        $this->helperData = $helperData;
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
        $output['quoteLifetime'] = $this->helperData->getQuoteLifetime();
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
        $quote = $this->checkoutSession->getQuote();

        if (!$this->isCustomerLoggedIn() && $quote->getId()) {
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
        $quote = $this->checkoutSession->getQuote();

        if ($this->isCustomerLoggedIn() && $quote->getId()) {
            if ($originatingQuoteId = (int) $quote->getOriginatingQuoteId()) {
                $order = $this->order->loadByIncrementId($originatingQuoteId);
                $customerShippingAddress = $this->fillAddressFields($quote);
                $customerShippingAddress['address_id'] = $order->getShippingAddress() ?
                    (int) $order->getShippingAddress()->getCustomerAddressId() :
                    '';
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
