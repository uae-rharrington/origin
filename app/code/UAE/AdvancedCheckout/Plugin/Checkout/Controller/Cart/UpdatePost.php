<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

namespace UAE\AdvancedCheckout\Plugin\Checkout\Controller\Cart;

class UpdatePost
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
    }

    /**
     * Count cart updates to ensure 'Continue shopping' link goes all the way back to previous page
     */
    public function beforeExecute(\Magento\Checkout\Controller\Cart\UpdatePost $subject)
    {
        $previousPageUrl = $this->_customerSession->getContinueShoppingUrl();
        $cartUpdates = $this->_customerSession->getCartUpdates();

        // Set continue-shopping URL on new checkout session based on value saved from initial cart visit
        $this->_checkoutSession->setContinueShoppingUrl($previousPageUrl);

        // Increment cart-update number on customer session to be used by \UAE\AdvancedCheckout\Block\Checkout\Cart\Grid::getCartUpdates()
        $this->_customerSession->setCartUpdates(isset($cartUpdates) ? $cartUpdates + 1 : 0);

        return null;
    }
}
