<?php
/**
 * Email Order Observer
 *
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
namespace UAE\QuoteCustom\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * UAE\QuoteCustom\Observer\EmailOrder
 *
 * @category UAE
 * @package UAE_QuoteCustom
 */
class EmailOrder implements ObserverInterface
{
    /** Quote Lifetime Config Path */
    const XML_PATH_QUOTE_LIFETIME = 'checkout/cart/delete_quote_after';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * EmailOrder constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Set quote expiry date
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();

        if ($transport->getOrder()->getIsQuoteRequest()) {
            $transport->setData('quoteLifetime',
                $this->scopeConfig->getValue(
                    self::XML_PATH_QUOTE_LIFETIME,
                    ScopeInterface::SCOPE_STORE
                )
            );
        }
    }
}
