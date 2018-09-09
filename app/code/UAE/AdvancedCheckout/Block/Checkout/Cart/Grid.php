<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */
namespace UAE\AdvancedCheckout\Block\Checkout\Cart;

/**
 * @inheritdoc
 */
class Grid extends \Magento\Checkout\Block\Cart\Grid
{
    /**
     * @return string
     */
    public function getCartUpdates()
    {
        $cartUpdates = $this->getData('cart_updates');
        if ($cartUpdates === null) {
            $cartUpdates = $this->_customerSession->getCartUpdates();
            if (!isset($cartUpdates)) {
                $cartUpdates = 0;
            }
            $this->setData('cart_updates', $cartUpdates);
        }
        return $cartUpdates;
    }
}
