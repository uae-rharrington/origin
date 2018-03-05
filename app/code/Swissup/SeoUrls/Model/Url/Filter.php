<?php
/**
 * Generate URLs for layered navigation filters, remove filter and clear all
 */

namespace Swissup\SeoUrls\Model\Url;

class Filter extends \Magento\Framework\Url
{
    /**
     * Build URLs for layered navigation on product listing and search page
     * @param   string|null $routePath
     * @param   array|null $routeParams
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        $seoHelper = $this->getData('seoHelper');
        if (!isset($seoHelper) // seoHelper is undefined
            || !$seoHelper->isSeoUrlsEnabled() // module disabled
            || !$this->_getRequest()->getAlias(self::REWRITE_REQUEST_PATH_ALIAS)
               // there is no alias for URL; perhaps URL is catalog/category/view/id
        ) {
            return parent::getUrl($routePath, $routeParams);
        }

        $query = !empty($routeParams['_current']) // add current request values
            ? $this->getFiltersStateAsQuery()
            : [];
        if (isset($routeParams['_query'])) {
            $query = array_merge($query, $routeParams['_query']);
        }

        // prepare url part with filters
        $urlFilters = [];
        foreach ($query as $key => $value) {
            if (isset($value)) {
                $seoFilter = $seoHelper->getByName($key);
                if (isset($seoFilter)) {
                    $sortOrder = $seoFilter->getSortOrder();
                    $urlFilters[$sortOrder] = $this->getPairFilterValue($seoFilter, $value);
                    // unset processed filter
                    // unset($query[$key]);
                    $query[$key] = null;
                }
            }
        }

        ksort($urlFilters);
        if ($seoHelper->isSeparateFilters() && !empty($urlFilters)) {
            array_unshift($urlFilters, $seoHelper->getFiltersSeparator());
        }

        // overwrite query for url
        $this->_queryParamsResolver->setQueryParams($query);
        $routeParams['_query'] = $query;
        // get url using parent method
        $url = parent::getUrl($routePath, $routeParams);
        // rebuild url
        return $this->getData('seoUrl')->rebuild($url, $urlFilters);
    }

    /**
     * Get pair filet-value for seo url
     *
     * @param  \Swissup\SeoUrls\Model\Filter\AbstractFilter $filter
     * @param  string $value
     * @return string
     */
    public function getPairFilterValue(
        \Swissup\SeoUrls\Model\Filter\AbstractFilter $filter,
        $value
    ) {
        $options = $filter->getOptions();
        if (is_array($value)) {
            $value = implode('-', $value);
        }

        $seoValue = '';
        if ($options) {
            if (isset($options[$value])) {
                // default magento layered navigation
                $seoValue = $options[$value];
            } else {
                // swissup ajax layered navigation
                // or other LN that allows select multiple values
                $valueArray = explode(',', $value);
                $v = '';
                $seoVs = [];
                do {
                    $v.= array_shift($valueArray);
                    if ($v !== null && isset($options[$v])) {
                        $seoVs[$v] = $options[$v];
                        $v = '';
                    } else {
                        $v.= ',';
                    }
                } while (!empty($valueArray));
                ksort($seoVs);
                $seoValue = implode('-', $seoVs);
            }
        } else {
            $seoValue = $value;
        }

        return $filter->getLabel() . '-' . $seoValue;
    }

    /**
     * Get filters state as query array
     *
     * @return array
     */
    public function getFiltersStateAsQuery()
    {
        $q = [];
        $catalogLayer = $this->getData('layerResolver')->get();
        foreach ($catalogLayer->getState()->getFilters() as $f) {
            $name = $f->getFilter()->getRequestVar();
            if (isset($q[$name])) {
                $q[$name].= ',' . $f->getValue();
            } else {
                $q[$name]= $f->getValue();
            }
        }

        return $q;
    }
}
