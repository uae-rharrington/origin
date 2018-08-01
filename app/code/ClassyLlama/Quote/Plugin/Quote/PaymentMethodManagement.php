<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Plugin\Quote;

class PaymentMethodManagement
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     */
    public function __construct(\Magento\Quote\Api\CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * Prepares quote with method isQuoteRequest value @see \Magento\Quote\Model\PaymentMethodManagement::set
     *
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $method
     * @return array
     */
    public function beforeSet(
        \Magento\Quote\Model\PaymentMethodManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $method
    ) {
        $extensionAttributes = !is_null($method) ? $method->getExtensionAttributes() : false;
        if ($extensionAttributes) {
            $isQuoteRequest = $extensionAttributes->getIsQuoteRequest();
            if ($isQuoteRequest === true) {
                /** @var \Magento\Quote\Model\Quote $quote */
                $cartQuote = $this->cartRepository->get($cartId);
                $cartQuote->setIsQuoteRequest($isQuoteRequest);
            }
        }
        return [$cartId, $method];
    }
}
