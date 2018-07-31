<?php
/**
 * LayoutProcessor
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\OrderComments\Plugin\Block\Checkout;

use ClassyLlama\Quote\Block\Checkout\LayoutProcessor as QuoteLayoutProcessor;
use ClassyLlama\Quote\Helper\Data;

/**
 * ClassyLlama\OrderComments\Plugin\Block\Checkout\LayoutProcessor
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 */
class LayoutProcessor
{
    /**
     * Comment Quote Label Value
     */
    const COMMENT_QUOTE_LABEL = 'Notes About Your Quote';

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->dataHelper = $data;
    }

    /**
     * @param QuoteLayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        QuoteLayoutProcessor $subject,
        $jsLayout
    ) {
        if ($this->dataHelper->getIsQuoteRequest()) {
            $jsLayout['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']['shippingAdditional']
            ['children']['order_message']['config']['label'] = self::COMMENT_QUOTE_LABEL;
        }

        return $jsLayout;
    }
}