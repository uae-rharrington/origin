<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */
namespace UAE\AdvancedCheckout\Controller\Sidebar;

use Magento\Checkout\Model\Sidebar;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Update Item Qty
 */
class UpdateItemQty extends Action
{
    /**
     * @var Sidebar
     */
    protected $sidebar;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @param Context $context
     * @param Sidebar $sidebar
     * @param Cart $cart
     * @param LoggerInterface $logger
     * @param Data $jsonHelper
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Sidebar $sidebar,
        Cart $cart,
        LoggerInterface $logger,
        Data $jsonHelper
    ) {
        $this->sidebar = $sidebar;
        $this->cart = $cart;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * @return Http
     */
    public function execute()
    {
        $itemId = (int)$this->getRequest()->getParam('item_id');
        $itemQty = (int)$this->getRequest()->getParam('item_qty');

        try {
            $this->sidebar->checkQuoteItem($itemId);
            $this->sidebar->updateQuoteItem($itemId, $itemQty);
            $item = $this->cart->getQuote()->getItemById($itemId);
            return $this->jsonResponse(['price' => $item->getPrice(), 'subtotal' => $item->getRowTotal()]);
        } catch (LocalizedException $e) {
            return $this->jsonResponse([], $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse([], $e->getMessage());
        }
    }

    /**
     * Compile JSON response
     *
     * @param array $itemData
     * @param string $error
     * @return Http
     */
    protected function jsonResponse($itemData, $error = '')
    {
        $response = $this->sidebar->getResponseData($error);
        $response['itemData'] = $itemData;

        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
