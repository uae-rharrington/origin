<?php

namespace Swissup\Ajaxsearch\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_FOLDED_ENABLE = 'ajaxsearch/folded/enable';

    /**
     * @var string
     */
    const CONFIG_PATH_FOLDED_EFFECT = 'ajaxsearch/folded/effect';

    /**
     * @var string
     */
    const CONFIG_PATH_AUTOCOMPLETE_ENABLE = 'ajaxsearch/autocomplete/enable';

    /**
     * @var string
     */
    const CONFIG_PATH_AUTOCOMPLETE_LIMIT  = 'ajaxsearch/autocomplete/limit';

    /**
     * @var string
     */
    const CONFIG_PATH_PRODUCT_ENABLE = 'ajaxsearch/product/enable';

    /**
     * @var string
     */
    const CONFIG_PATH_PRODUCT_LIMIT  = 'ajaxsearch/product/limit';

    /**
     * @var string
     */
    const CONFIG_PATH_CATEGORY_ENABLE = 'ajaxsearch/category/enable';

    /**
     * @var string
     */
    const CONFIG_PATH_CATEGORY_LIMIT  = 'ajaxsearch/category/limit';

    /**
     * @var string
     */
    const CONFIG_PATH_PAGE_ENABLE = 'ajaxsearch/page/enable';

    /**
     * @var string
     */
    const CONFIG_PATH_PAGE_LIMIT  = 'ajaxsearch/page/limit';

    /**
     *
     * @param  string $key
     * @return mixed
     */
    private function getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve folded design flag
     *
     * @return boolean
     */
    public function isFoldedDesignEnabled()
    {
        return (bool) $this->getConfig(self::CONFIG_PATH_FOLDED_ENABLE);
    }

    /**
     * Get Folded Effect
     *
     * @return string
     */
    public function getFoldedEffect()
    {
        return $this->getConfig(self::CONFIG_PATH_FOLDED_EFFECT);
    }

    /**
     * Retrieve ajaxsearch block additional css classes
     *
     * @return string
     */
    public function getAdditionalCssClass()
    {
        $classes = [];
        if ($this->isFoldedDesignEnabled()) {
            $classes[] = 'folded';
            $classes[] = $this->getFoldedEffect();
        }
        return implode(' ', $classes);
    }

    /**
     * Retrieve autocomplete enable
     *
     * @return boolean
     */
    public function isAutocompleteEnabled()
    {
        return (bool) $this->getConfig(self::CONFIG_PATH_AUTOCOMPLETE_ENABLE);
    }

    /**
     * Get Autocomplete limit
     *
     * @return int
     */
    public function getAutocompleteLimit()
    {
        return (int) $this->getConfig(self::CONFIG_PATH_AUTOCOMPLETE_LIMIT);
    }

    /**
     * Retrieve product enable
     *
     * @return boolean
     */
    public function isProductEnabled()
    {
        return (bool) $this->getConfig(self::CONFIG_PATH_PRODUCT_ENABLE);
    }

    /**
     * Get Product limit
     *
     * @return int
     */
    public function getProductLimit()
    {
        return (int) $this->getConfig(self::CONFIG_PATH_PRODUCT_LIMIT);
    }

    /**
     * Retrieve category enable
     *
     * @return boolean
     */
    public function isCategoryEnabled()
    {
        return (bool) $this->getConfig(self::CONFIG_PATH_CATEGORY_ENABLE);
    }

    /**
     * Get category limit
     *
     * @return int
     */
    public function getCategoryLimit()
    {
        return (int) $this->getConfig(self::CONFIG_PATH_CATEGORY_LIMIT);
    }

    /**
     * Retrieve cms page search enable
     *
     * @return boolean
     */
    public function isPageEnabled()
    {
        return (bool) $this->getConfig(self::CONFIG_PATH_PAGE_ENABLE);
    }

    /**
     * Get cms page search limit
     *
     * @return int
     */
    public function getPageLimit()
    {
        return (int) $this->getConfig(self::CONFIG_PATH_PAGE_LIMIT);
    }
}
