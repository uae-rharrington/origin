<?php
/**
 * Shipping Information Management
 *
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\QuoteCustom\Plugin\Model;

use Magento\Checkout\Model\ShippingInformationManagement as ShippingInformation;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Customer\Model\Session;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;

/**
 * ClassyLlama\QuoteCustom\Plugin\Model\ShippingInformationManagement
 *
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 */
class ShippingInformationManagement
{
    /**  Quote Payment Method Value */
    const QUOTE_PAYMENT_METHOD = 'quoterequest';

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var GuestPaymentInformationManagementInterface
     */
    private $guestPaymentInformationManagement;

    /**
     * @var PaymentInformationManagementInterface
     */
    private $paymentInformationManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var ExtensionAttributesFactory
     */
    private $extensionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ShippingInformationManagement constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param Session $customerSession
     * @param GuestPaymentInformationManagementInterface $guestPaymentInformationManagement
     * @param PaymentInformationManagementInterface $paymentInformationManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param ExtensionAttributesFactory $extensionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Session $customerSession,
        GuestPaymentInformationManagementInterface $guestPaymentInformationManagement,
        PaymentInformationManagementInterface $paymentInformationManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        ExtensionAttributesFactory $extensionFactory,
        LoggerInterface $logger
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->customerSession = $customerSession;
        $this->guestPaymentInformationManagement = $guestPaymentInformationManagement;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->extensionFactory = $extensionFactory;
        $this->logger = $logger;
    }

    /**
     * Around Plugin
     * Save Address Information
     *
     * @param ShippingInformation $subject
     * @param \Closure $proceed
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @return PaymentDetailsInterface $result
     */
    public function aroundSaveAddressInformation(
        ShippingInformation $subject,
        \Closure $proceed,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $address = $addressInformation->getShippingAddress();
        $result = $proceed($cartId, $addressInformation);

        $isQuoteRequest = $address->getExtensionAttributes()->getIsQuoteRequest();
        if ($isQuoteRequest) {
            try {
                /** @var Quote $quote */
                $quote = $this->quoteRepository->getActive($cartId);
                $payment = $quote->getPayment();
                $payment->setMethod(self::QUOTE_PAYMENT_METHOD);
                $payment->setPoNumber(null);
                $payment->setAdditionalData(null);
                $extensionAttributes = $payment->getExtensionAttributes();
                if ($extensionAttributes === null) {
                    $extensionAttributes = $this->extensionFactory->create(PaymentInterface::class);
                    $payment->setExtensionAttributes($extensionAttributes);
                }
                if ($extensionAttributes->getIsQuoteRequest() === null) {
                    $extensionAttributes->setIsQuoteRequest($isQuoteRequest);
                }

                if ($this->customerSession->isLoggedIn()) {
                    $this->paymentInformationManagement
                        ->savePaymentInformationAndPlaceOrder($quote->getEntityId(), $payment, $address);
                } else {
                    $quoteIdMask = $this->quoteIdMaskFactory->create()
                        ->load($quote->getEntityId(), 'quote_id');
                    $email = $address->getExtensionAttributes()->getCustomerEmail();
                    $this->guestPaymentInformationManagement
                        ->savePaymentInformationAndPlaceOrder($quoteIdMask->getMaskedId(), $email, $payment, $address);
                }
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        return $result;
    }
}