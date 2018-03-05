<?php

namespace Swissup\SeoUrls\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Swissup\SeoUrls\Model\Layer\PredefinedFilters
     */
    protected $predefinedFiltersList;

    /**
     * Constructor
     *
     * @param \Swissup\SeoUrls\Model\Layer\PredefinedFilters $predefinedFilters
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Swissup\SeoUrls\Model\Layer\PredefinedFilters $predefinedFilters,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->predefinedFiltersList = $predefinedFilters;
        parent::__construct($context);
    }

    /**
     * Convert given string into seo string for url
     *
     * @param  string $string
     * @return string
     */
    public function getSeoFriendlyString($string)
    {
        // remove leading and trailing spaces
        $string = trim($string);
        // source - https://stackoverflow.com/questions/11330480/strip-php-variable-replace-white-spaces-with-dashes
        // Lower case everything
        $string = strtolower($string);
        // decode html entities to utf8
        $string = html_entity_decode($string);
        // Remove & and .
        $string = preg_replace("/[&.]/", " ", $string);
        // Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        // Convert whitespaces, underscore and slash to dash
        $string = preg_replace("/[\s_\/]/", "-", $string);
        // workaround to replace first dash with minus sign "−" (HTML: &#8722;)
        if (strpos($string, '-') === 0) {
            $string = '−' . substr($string, 1);
        }
        return $string;
    }

    /**
     * Get predefined layer filter seo label
     *
     * @param  string $filterName
     * @return string
     */
    public function getPredefinedFilterLabel($filterName)
    {
        if ($this->predefinedFiltersList->hasData($filterName)) {
            $label = $this->predefinedFiltersList->getData($filterName)
                ->getStoreLabel();
            return $this->getSeoFriendlyString($label);
        }

        return '';
    }

    /**
     * Get predefined layer filter request var
     *
     * @param  string $filterName
     * @return string
     */
    public function getPredefinedFilterRequestVar($filterName)
    {
        if ($this->predefinedFiltersList->hasData($filterName)) {
            return $this->predefinedFiltersList->getData($filterName)
                ->getRequestVar();
        }

        return '';
    }

    /**
     * Check if SEO URLs enabled
     *
     * @return boolean
     */
    public function isSeoUrlsEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            'swissup_seourls/general/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get name for search controller in URL
     *
     * @return string
     */
    public function getSearchControllerName()
    {
        $name = $this->scopeConfig->getValue(
            'swissup_seourls/search/controller_name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (strpos($name, '.') === false) {
            // no extension
            $name = rtrim($name, '/') . '/';
        }

        return $name;
    }

    /**
     * Check config option if search term show in url body
     *
     * @return boolean
     */
    public function isSearchTermInUrl()
    {
        return (bool)$this->scopeConfig->getValue(
            'swissup_seourls/search/term_place',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get catgeory by its ID
     *
     * @param  int|string $id
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    public function getCategoryById($id)
    {
        return $this->categoryRepository->get(
            $id,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * Get root catgeory for current store
     *
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    public function getRootCategory()
    {
        return $this->getCategoryById(
            $this->storeManager->getStore()->getRootCategoryId()
        );
    }

    public function isSeparateFilters()
    {
        return $this->scopeConfig->getValue(
                'swissup_seourls/layered_navigation/separate_filters',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
            &&
            $this->scopeConfig->getValue(
                'swissup_seourls/layered_navigation/separator',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    public function getFiltersSeparator()
    {
        $separator = $this->scopeConfig->getValue(
            'swissup_seourls/layered_navigation/separator',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return trim($separator, '/');
    }

    /**
     * Get CMS page identifier for homapage
     *
     * @return string
     */
    public function getHomepageIdentifier()
    {
        return $this->scopeConfig->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get URL to homepage
     *
     * @return string
     */
    public function getHomepageUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    public function isHomepageRedirect()
    {
        return $this->isSeoUrlsEnabled()
            && $this->scopeConfig->getValue(
                'swissup_seourls/cms/redirect_to_home',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

}
