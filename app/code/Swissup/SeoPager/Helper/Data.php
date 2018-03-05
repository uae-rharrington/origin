<?php

namespace Swissup\SeoPager\Helper;

use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Get URL to "view all" page
     *
     * @param  boolean $addCurrentRequestParams
     * @return string
     */
    public function getViewAllPageUrl($addCurrentRequestParams = true)
    {
        $urlParams = [
            '_current' => $addCurrentRequestParams,
            '_escape' => true,
            '_use_rewrite' => true,
            '_query' => [
                    '_' => null, // remove mystic underscore param
                    ToolbarModel::PAGE_PARM_NAME => null, // remove page param
                    ToolbarModel::LIMIT_PARAM_NAME => 'all'
                ]
        ];
        return $this->_getUrl('*/*/*', $urlParams);
    }

    /**
     * Is view all button for pagination available
     *
     * @return boolean
     */
    public function canShowViewAllLink()
    {
        return $this->scopeConfig->isSetFlag(
            'catalog/frontend/list_allow_all',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get strategy of paginated content presentation for search engines
     *
     * @return string
     */
    public function getPresentationStrategy()
    {
        if ($this->canShowViewAllLink()) {
            return $this->scopeConfig->getValue(
                'swissup_seopager/general/strategy',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return $this->scopeConfig->getValue(
            'swissup_seopager/general/strategy_no_view_all',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
