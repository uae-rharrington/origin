<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */
namespace UAE\Theme\Helper;

/**
 * Theme helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Get store phone number
     *
     * @return string | null
     */
    public function getStorePhone()
    {
        return $this->scopeConfig->getValue('general/store_information/phone');
    }
}
