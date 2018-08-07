<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Controller\Cart;

use ClassyLlama\Quote\Helper\AddQuoteRequestToCart;

class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * Message to display when adding quote request items to a cart already populated with other items
     */
    const EXISTING_ITEMS_MESSAGE = 'You had existing items in your shopping cart. We merged those items with the items '
        . 'from the quote. If you\'d like to only see items from your quote, click the "Clear Shopping Cart" button at '
        . 'the bottom of this page and then add the quote back to your cart using the link you just clicked on.';

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    private $cartHelper;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \ClassyLlama\Quote\Helper\AddQuoteRequestToCart
     */
    private $addQuoteRequestToCartHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param AddQuoteRequestToCart $addQuoteRequestToCart
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        AddQuoteRequestToCart $addQuoteRequestToCart
    ) {
        parent::__construct($context);

        $this->cartHelper = $cartHelper;
        $this->orderRepository = $orderRepository;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->addQuoteRequestToCartHelper = $addQuoteRequestToCart;
    }

    /**
     * Add Quote Request Items Contained in Order Id to current Customer's Cart
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $results = false;
        $orderId = $this->getRequest()->getParam('id');
        $orderIncrementId = '';

        if ($orderId) {
            $order = $this->orderRepository->get($orderId);
            $orderIncrementId = $order->getIncrementId();
            $cart = $this->cartHelper->getCart();
            $results = $this->addQuoteRequestToCartHelper->execute($order, $cart);
        }

        if ($results) {
            if (isset($results[AddQuoteRequestToCart::RESULTS_ERRORS_KEY])) {
                if ($this->checkoutSession->getUseNotice(true)) {
                    foreach ($results[AddQuoteRequestToCart::RESULTS_ERRORS_KEY] as $sku => $message) {
                        $this->messageManager->addNoticeMessage(__($message . " (SKU: $sku )"));
                    }
                } else {
                    foreach ($results[AddQuoteRequestToCart::RESULTS_ERRORS_KEY] as $sku => $message) {
                        $this->messageManager->addErrorMessage(__($message . " (SKU: $sku )"));
                    }
                }
            }
            if (isset($results[AddQuoteRequestToCart::RESULTS_EXCEPTIONS_KEY])) {
                foreach ($results[AddQuoteRequestToCart::RESULTS_EXCEPTIONS_KEY] as $sku => $exception) {
                    $this->messageManager->addExceptionMessage(
                        $exception,
                        __('We can\'t add this Quote Request Item (SKU: '. $sku . ') to your shopping cart right now.')
                    );
                }
            }
            if (isset($results[AddQuoteRequestToCart::RESULTS_SUCCESSES_KEY])) {
                if ($results[AddQuoteRequestToCart::RESULTS_CART_CONTAINED_ITEMS_KEY]) {
                    $this->messageManager->addErrorMessage(__(self::EXISTING_ITEMS_MESSAGE));
                }
                $this->messageManager->addSuccessMessage(__("Quote Request (#$orderIncrementId) was added to Cart."));
            }
        } else {
            $this->messageManager->addErrorMessage(__('We could not process this Quote Request at this time.'));
        }

        return $resultRedirect->setPath('checkout/cart');
    }
}
