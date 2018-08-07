<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Plugin\Checkout\Controller\Onepage;

class Success
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    /**
     * If order is a Quote Request we reroute to the quote request success controller
     *
     * @param \Magento\Checkout\Controller\Onepage\Success $subject
     * @param \Closure $closure
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function aroundExecute(\Magento\Checkout\Controller\Onepage\Success $subject, \Closure $closure)
    {
        if ($subject->getOnepage()->getCheckout()->getLastRealOrder()->getIsQuoteRequest()) {
            return $this->resultRedirectFactory->create()->setPath('quoterequest/onepage/success');
        } else {
            return $closure();
        }
    }
}
