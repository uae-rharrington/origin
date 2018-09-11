<?php
/**
 * Cart Totals Retriever
 *
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace UAE\QuoteCustom\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Quote\Api\Data\TotalsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magento\Quote\Model\Cart\TotalsConverter;
use Magento\Quote\Api\CouponManagementInterface;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;
use ClassyLlama\Quote\Helper\AddQuoteRequestToCart;

/**
 * UAE\QuoteCustom\Model\CartTotalsRetriever
 *
 * @category UAE
 * @package UAE_QuoteCustom
 */
class CartTotalsRetriever
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Shipping method converter
     *
     * @var ShippingMethodConverter
     */
    private $converter;

    /**
     * Cart totals factory.
     *
     * @var TotalsInterfaceFactory
     */
    private $totalsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ConfigurationPool
     */
    private $itemConverter;

    /**
     * @var TotalsConverter
     */
    protected $totalsConverter;

    /**
     * @var CouponManagementInterface
     */
    protected $couponService;

    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AddQuoteRequestToCart
     */
    private $quoteRequestHelper;

    /**
     * CartTotalsRetriever constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param ShippingMethodConverter $converter
     * @param TotalsInterfaceFactory $totalsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ItemConverter $itemConverter
     * @param TotalsConverter $totalsConverter
     * @param CouponManagementInterface $couponService
     * @param OrderInterface $order
     * @param LoggerInterface $logger
     * @param AddQuoteRequestToCart $quoteRequestHelper
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        ShippingMethodConverter $converter,
        TotalsInterfaceFactory $totalsFactory,
        DataObjectHelper $dataObjectHelper,
        ItemConverter $itemConverter,
        TotalsConverter $totalsConverter,
        CouponManagementInterface $couponService,
        OrderInterface $order,
        LoggerInterface $logger,
        AddQuoteRequestToCart $quoteRequestHelper
    ){
        $this->quoteRepository = $quoteRepository;
        $this->converter = $converter;
        $this->totalsFactory = $totalsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->itemConverter = $itemConverter;
        $this->totalsConverter = $totalsConverter;
        $this->order = $order;
        $this->logger = $logger;
        $this->quoteRequestHelper = $quoteRequestHelper;
    }

    /**
     * Retrieve cart total
     *
     * @param $cartId
     * @return TotalsInterface
     */
    public function getCartTotal($cartId)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        if ($quote->isVirtual()) {
            $addressTotalsData = $quote->getBillingAddress()->getData();
            $addressTotals = $quote->getBillingAddress()->getTotals();
        } else {
            $addressTotalsData = $quote->getShippingAddress()->getData();
            $addressTotals = $quote->getShippingAddress()->getTotals();
        }
        unset($addressTotalsData[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);

        /** @var TotalsInterface $quoteTotals */
        $quoteTotals = $this->totalsFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteTotals,
            $addressTotalsData,
            TotalsInterface::class
        );
        $items = [];
        foreach ($quote->getAllVisibleItems() as $index => $item) {
            $items[$index] = $this->itemConverter->modelToDataObject($item);
        }
        $calculatedTotals = $this->totalsConverter->process($addressTotals);
        $quoteTotals->setTotalSegments($calculatedTotals);

        $amount = $quoteTotals->getGrandTotal() - $quoteTotals->getTaxAmount();
        $amount = $amount > 0 ? $amount : 0;
        $quoteTotals->setCouponCode($quote->getCouponCode());
        $quoteTotals->setGrandTotal($amount);
        $quoteTotals->setItems($items);
        $quoteTotals->setItemsQty($quote->getItemsQty());
        $quoteTotals->setBaseCurrencyCode($quote->getBaseCurrencyCode());
        $quoteTotals->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());

        return $quoteTotals;
    }

    /**
     * Check quote
     *
     * @param $cartId
     * @return bool | int
     */
    public function checkQuote($cartId)
    {
        $changed = false;
        try {
            /** @var Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);

            if ($originatingQuoteId = $quote->getOriginatingQuoteId()) {
                $order = $this->order->loadByIncrementId((int)$originatingQuoteId);
                $items = $quote->getItemsSummaryQty() === (int) $order->getTotalQtyOrdered();

                $createdAt = new \DateTime($order->getCreatedAt(), new \DateTimeZone('UTC'));
                $now = new \DateTime('now', new \DateTimeZone('UTC'));
                $dateTimeDelta = $createdAt->diff($now);
                $isStale = $dateTimeDelta->days > $this->quoteRequestHelper->getQuoteSaleLifetime();

                $quoteShipping = $quote->getShippingAddress();
                $orderShipping = $order->getShippingAddress();
                $shippingMethod = $quoteShipping->getShippingMethod() === $order->getShippingMethod();

                $address = $quoteShipping->getCountryId() === $orderShipping->getCountryId() &&
                    $quoteShipping->getRegionId() === $orderShipping->getRegionId() &&
                    $quoteShipping->getCity() === $orderShipping->getCity() &&
                    $quoteShipping->getPostcode() === $orderShipping->getPostcode() &&
                    $quoteShipping->getStreet() === $quoteShipping->getStreet();

                if ($items && !$isStale && $shippingMethod && $address) {
                    $changed = $order->getQuoteId();
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $changed;
    }
}
