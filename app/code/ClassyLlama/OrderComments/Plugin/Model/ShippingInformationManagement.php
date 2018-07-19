<?php
/**
 * Shipping Information Management
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\OrderComments\Plugin\Model;

use Magento\Checkout\Model\ShippingInformationManagement as ShippingInformation;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Checkout\Api\Data\PaymentDetailsInterface;

/**
 * ClassyLlama\OrderComments\Plugin\Model\ShippingInformationManagement
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 */
class ShippingInformationManagement
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ShippingInformationManagement constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->quoteRepository = $quoteRepository;
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
     * @throws InputException
     */
    public function aroundSaveAddressInformation(
        ShippingInformation $subject,
        \Closure $proceed,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $address = $addressInformation->getShippingAddress();
        $result = $proceed($cartId, $addressInformation);

        $orderComment = $address->getExtensionAttributes()->getOrderComment();
        if ($orderComment) {
            /** @var Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            $quote->setOrderComment($orderComment);
            try {
                $this->quoteRepository->save($quote);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new InputException(__('Unable to save shipping information. Please check input data.'));
            }
        }

        return $result;
    }
}