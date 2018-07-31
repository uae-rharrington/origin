<?php
/**
 * Block Quote Expire
 *
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\QuoteCustom\Block\Quote;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;

/**
 * ClassyLlama\QuoteCustom\Block\Quote\Expire
 *
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 */
class Expire extends Template
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * Expire constructor.
     *
     * @param Template\Context $context
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Check Is Quote Expired
     *
     * @return boolean $isStale
     */
    public function isQuoteExpired() {
        $isStale = $this->customerSession->getIsQuoteExpired();
        $this->customerSession->setIsQuoteExpired(false);

        return $isStale;
    }
}