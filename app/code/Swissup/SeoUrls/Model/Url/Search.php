<?php
/**
 * Generate search URL
 */
namespace Swissup\SeoUrls\Model\Url;

class Search extends \Magento\Framework\Url
{
    /**
     * Build URLs for search form
     *
     * @param   string|null $routePath
     * @param   array|null $routeParams
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        $seoHelper = $this->getData('seoHelper'); // declared in di.xml
        if (isset($seoHelper) && $seoHelper->isSeoUrlsEnabled()) {
            if ($routePath == 'catalogsearch/result') {
                $target = $seoHelper->getSearchControllerName();
                $routePath = '';
                $routeParams = ['_direct' => $target];
            } elseif ($seoHelper->isSearchTermInUrl()) {
                if (isset($routeParams['_query'])) {
                    $routeParams['_query']['q'] = null;
                }
            }
        }

        return parent::getUrl($routePath, $routeParams);
    }
}
