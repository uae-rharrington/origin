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
     * AddQuoteRequestToCart constructor.
     *
     * @param Session $customerSession
     */
    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * Check for expired
     *
     * @param AddQuoteRequestToCartHelper $subject
     * @param bool|array $result
     * @param OrderInterface $order
     * @param Cart $cart
     *
     * @return bool|array
     */
    public function afterExecute(
        AddQuoteRequestToCartHelper $subject,
        $result,
        OrderInterface $order,
        Cart $cart
    ) {
        if ($result !== false && isset($result[AddQuoteRequestToCartHelper::RESULTS_SUCCESSES_KEY])) {
            $createdAt = new \DateTime($order->getCreatedAt(), new \DateTimeZone('UTC'));
            $now = new \DateTime('now', new \DateTimeZone('UTC'));
            $dateTimeDelta = $createdAt->diff($now);
            $isStale = $dateTimeDelta->days > AddQuoteRequestToCartHelper::QUOTE_REQUEST_STALE_DAYS;
            $this->customerSession->setIsQuoteExpired($isStale);
        }

        return $result;
    }
}