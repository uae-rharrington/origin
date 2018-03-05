<?php
namespace Swissup\Ajaxsearch\Model\Query\Cms;

use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Model\ResourceModel\Page\Grid\Collection;
use Magento\Search\Model\SearchCollectionFactory as CollectionFactory;
use Swissup\Ajaxsearch\Model\Query\CollectionFactory as QueryCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb as DbCollection;
use Magento\Framework\Model\ResourceModel\AbstractResource;

use Swissup\Ajaxsearch\Helper\Data as ConfigHelper;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Magento\Framework\Api\FilterBuilder;

/**
 * Search query model
 */
class Page extends \Swissup\Ajaxsearch\Model\Query\WithFilterPool
{
    /**
     *
     * @var string
     */
    protected $name = 'cms_page_listing_data_source';

    /**
     * Retrieve suggest collection for query
     *
     * @return Collection
     */
    protected function _getSuggestCollection()
    {
        $collection = $this->_queryCollectionFactory
            ->setInstanceName(Collection::class)
            ->create()
            // ->setStoreId($this->getStoreId())
            ;
        $limit = $this->configHelper->getPageLimit();
        if ($limit) {
            $collection->setPageSize($limit);
        }

        return $this->applyFilters($collection);
    }

    /**
     *
     * @return array
     */
    protected function getFilters()
    {
        $filters = [];
        $value = $this->getQueryText();
        if ($value) {
            $filter = $this->filterBuilder
                ->setConditionType('fulltext')
                ->setField('fulltext')
                ->setValue($value)
                ->create();
            $filters[] = $filter;
        }
        return $filters;
    }
}
