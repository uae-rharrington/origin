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
use UAE\QuoteCustom\Model\CartTotalsRetriever;

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
     * @var CartTotalsRetriever
     */
    private $cartTotalsRetriever;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CheckoutSubmitBefore constructor.
     *
     * @param OrderInterface $order
     * @param CartRepositoryInterface $quoteRepository
     * @param CartTotalsRetriever $cartTotalsRetriever
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderInterface $order,
        CartRepositoryInterface $quoteRepository,
        CartTotalsRetriever $cartTotalsRetriever,
        LoggerInterface $logger
    ) {
        $this->order = $order;
        $this->quoteRepository = $quoteRepository;
        $this->cartTotalsRetriever = $cartTotalsRetriever;
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
        $quoteId = $quote->getId();
        if ($this->cartTotalsRetriever->checkQuote($quoteId)) {
            try {
                $originatingQuoteId = (int) $quote->getOriginatingQuoteId();
                $order = $this->order->loadByIncrementId($originatingQuoteId);
                $originQuote = $this->quoteRepository->get($order->getQuoteId());
                $quote->setData($originQuote->getData());
                $quote->setId($quoteId);
                $quote->setShippingAddress($originQuote->getShippingAddress());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }
}
