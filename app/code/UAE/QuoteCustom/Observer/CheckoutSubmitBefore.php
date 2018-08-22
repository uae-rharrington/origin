<?php
/**
 * Checkout Submit Before Observer
 *
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
namespace UAE\QuoteCustom\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * UAE\QuoteCustom\Observer\CheckoutSubmitBefore
 *
 * @category UAE
 * @package UAE_QuoteCustom
 */
class CheckoutSubmitBefore implements ObserverInterface
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CheckoutSubmitBefore constructor.
     *
     * @param OrderInterface $order
     * @param CartRepositoryInterface $quoteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderInterface $order,
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->order = $order;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * Check quote and set totals
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote->getOriginatingQuoteId()) {
            try {
                $originatingQuoteId = (int) $quote->getOriginatingQuoteId();
                $order = $this->order->loadByIncrementId($originatingQuoteId);
                $originQuote = $this->quoteRepository->get($order->getQuoteId());

                if (count($quote->getAllItems()) === count($order->getAllItems()) &&
                    $originQuote->getShippingMethod() ===  $quote->getShippingMethod()) {
                    $quote->setData($originQuote->getData());
                    $quote->setShippingAddress($originQuote->getShippingAddress());
                }
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }
}
