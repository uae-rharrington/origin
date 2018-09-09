<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

namespace UAE\AdvancedCheckout\Plugin\Checkout\Controller\Cart;

class Index
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $_redirect;

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
        $this->_redirect = $context->getRedirect();
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
    }

    /**
     * Set URL for 'Continue shopping' link to go to previous page
     */
    public function beforeExecute(\Magento\Checkout\Controller\Cart\Index $subject)
    {
        $previousPageUrl = $this->_redirect->getRefererUrl();

        // If initial cart visit
        if (strpos($previousPageUrl, $subject->getRequest()->getPathInfo()) === false) {
            // Set URL on checkout session to be used by \Magento\Checkout\Block\Cart::getContinueShoppingUrl()
            $this->_checkoutSession->setContinueShoppingUrl($previousPageUrl);

            // Set URL on customer session also, so it can persist through cart updates (which clear checkout sessions)
            $this->_customerSession->setContinueShoppingUrl($previousPageUrl);

            // Reset cart-update number
            $this->_customerSession->setCartUpdates(0);
        }

        return null;
    }
}
