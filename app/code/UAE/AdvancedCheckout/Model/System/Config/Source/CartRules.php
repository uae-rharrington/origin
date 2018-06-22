<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace UAE\AdvancedCheckout\Model\System\Config\Source;

/**
 * Source model for qualifying items cart price rules selector.
 */
class CartRules implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    private $cartRulesFactory;

    /**
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $cartRulesFactory
     */
    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $cartRulesFactory
    ) {
        $this->cartRulesFactory = $cartRulesFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $cartRules =  $this->cartRulesFactory->create();
        $options = [__('--Please Select--')];
        foreach ($cartRules as $rule) {
            $options[$rule->getId()] = $rule->getName();
        }

        return $options;
    }
}
