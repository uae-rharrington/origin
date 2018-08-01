<?php
/**
 * Shipping Information Management
 *
 * @category UAE
 * @package UAE_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace UAE\OrderComments\Plugin\Model;

use Magento\Checkout\Model\ShippingInformationManagement as ShippingInformation;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Checkout\Api\Data\PaymentDetailsInterface;

/**
 * UAE\OrderComments\Plugin\Model\ShippingInformationManagement
 *
 * @category UAE
 * @package UAE_OrderComments
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
     * After Plugin
     * Save Address Information
     *
     * @param ShippingInformation $subject
     * @param PaymentDetailsInterface $result
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @return PaymentDetailsInterface $result
     * @throws InputException
     */
    public function afterSaveAddressInformation(
        ShippingInformation $subject,
        PaymentDetailsInterface $result,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $address = $addressInformation->getShippingAddress();
        $orderComment = $address->getExtensionAttributes()->getOrderComment();
        if ($orderComment) {
            try {
                /** @var Quote $quote */
                $quote = $this->quoteRepository->getActive($cartId);
                $quote->setOrderComment($orderComment);
                $this->quoteRepository->save($quote);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new InputException(__('Unable to save shipping information. Please check input data.'));
            }
        }

        return $result;
    }
}
