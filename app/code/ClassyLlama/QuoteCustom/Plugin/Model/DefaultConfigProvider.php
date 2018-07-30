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
     * DefaultConfigProvider constructor.
     *
     * @param HttpContext $httpContext
     * @param CheckoutSession $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        HttpContext $httpContext,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->httpContext = $httpContext;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
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
        return $result;
    }

    /**
     * Retrieve guest shipping address
     *
     * @return array
     */
    private function getGuestShippingAddress()
    {
        $guestShippingAddress = [];
        $quoteId = $this->checkoutSession->getQuote()->getId();

        if (!$this->isCustomerLoggedIn() && $quoteId) {
            $quote = $this->quoteRepository->get($quoteId);
            $shippingAddress = $quote->getShippingAddress();

            if ($shippingAddress) {
                $guestShippingAddress['email'] = $shippingAddress->getEmail();
                $guestShippingAddress['company'] = $shippingAddress->getCompany();
                $guestShippingAddress['telephone'] = $shippingAddress->getTelephone();
                $guestShippingAddress['firstname'] = $shippingAddress->getFirstname();
                $guestShippingAddress['lastname'] = $shippingAddress->getLastname();
                $guestShippingAddress['street'] = $shippingAddress->getStreet();
                $guestShippingAddress['city'] = $shippingAddress->getCity();
                $guestShippingAddress['postcode'] = $shippingAddress->getPostcode();
                $guestShippingAddress['country_id'] = $shippingAddress->getCountryId();
                $guestShippingAddress['region'] = $shippingAddress->getRegion();
                $guestShippingAddress['region_id'] = $shippingAddress->getRegionId();
            }
        }

        return $guestShippingAddress;
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