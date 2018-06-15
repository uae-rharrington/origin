<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace UAE\AdvancedCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Config provider for qualifying items message.
 */
class QualifyingItemsConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\SalesRule\Model\RuleRepository
     */
    private $ruleRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceFormatter;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \UAE\AdvancedCheckout\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\RuleRepository $ruleRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter
     * @param \UAE\AdvancedCheckout\Model\Config $config
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\RuleRepository $ruleRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter,
        \UAE\AdvancedCheckout\Model\Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->ruleRepository = $ruleRepository;
        $this->checkoutSession = $checkoutSession;
        $this->priceFormatter = $priceFormatter;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        $config = [];
        if ($rule = $this->getQualifyingItemsRule()) {
            $threshold = $this->getConditionAmount($rule);
            $regularTotal = $this->getRegularItemsTotal();
            if ($threshold && $threshold > $regularTotal) {
                $diff = $this->priceFormatter->format(
                    $threshold - $regularTotal,
                    false,
                    \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
                    null,
                    $this->storeManager->getStore()->getBaseCurrency()
                );
                $config['qualifyingItems']['message'] = sprintf(
                    $this->getQualifyingItemsMessage(),
                    $diff,
                    $this->getDiscount($rule)
                );
            }
        }

        return $config;
    }

    /**
     * Get threshold for qualifying items rule.
     *
     * @param \Magento\SalesRule\Api\Data\RuleInterface $rule
     * @return float
     */
    private function getConditionAmount(\Magento\SalesRule\Api\Data\RuleInterface $rule)
    {
        $value = 0;
        if ($rule->getCondition()->getConditions()) {
            foreach ($rule->getCondition()->getConditions() as $condition) {
                if ($condition->getAttributeName() == 'base_row_total') {
                    $value = $condition->getValue();
                }
            }
        }

        return $value;
    }

    /**
     * Get rule discount.
     *
     * @param \Magento\SalesRule\Api\Data\RuleInterface $rule
     * @return string
     */
    private function getDiscount(\Magento\SalesRule\Api\Data\RuleInterface $rule)
    {
        $discount = '';
        if ($rule->getSimpleAction() == \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION) {
            $discount = number_format($rule->getDiscountAmount(), 0) . '%';
        } elseif ($rule->getSimpleAction() == \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION) {
            $discount = $this->priceFormatter->format(
                $rule->getDiscountAmount(),
                false,
                \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
                null,
                $this->storeManager->getStore()->getBaseCurrency()
            );
        }
        return $discount;
    }

    /**
     * Get qualifying items message.
     *
     * @return string
     */
    private function getQualifyingItemsMessage()
    {
        return $this->config->getQualifyingItemsMessage(
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getCode()
        );
    }

    /**
     * Get qualifying items rule.
     *
     * @return \Magento\SalesRule\Api\Data\RuleInterface|null
     */
    private function getQualifyingItemsRule()
    {
        $ruleId = $this->config->getQualifyingItemsRule(
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getCode()
        );
        try {
            $rule = $this->ruleRepository->getById($ruleId);
            return $rule;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Retrieve Quote object.
     *
     * @return \Magento\Quote\Model\Quote
     */
    private function getQuote()
    {
        if (!$this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }

    /**
     * Calculate regular items price total that are currently added to cart.
     *
     * @return float
     */
    private function getRegularItemsTotal()
    {
        $quote = $this->getQuote();
        $regularItemsTotal = 0;
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            if ($quoteItem->getProduct()->getOnSale() != 0) {
                $regularItemsTotal += $quoteItem->getRowTotal();
            }
        }

        return $regularItemsTotal;
    }
}
