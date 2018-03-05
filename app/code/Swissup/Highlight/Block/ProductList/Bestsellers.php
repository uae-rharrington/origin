<?php

namespace Swissup\Highlight\Block\ProductList;

class Bestsellers extends All
{
    const PAGE_TYPE = 'bestsellers';

    protected $widgetPageVarName = 'hbp';

    protected $widgetPriceSuffix = 'bestsellers';

    protected $widgetCssClass = 'highlight-bestsellers';

    public function getProductCollectionType()
    {
        return \Swissup\Highlight\Model\ResourceModel\Product\CollectionFactory::TYPE_BESTSELLERS;
    }

    /**
     * @param  \Swissup\Highlight\Model\ResourceModel\Product\Popular\Collection
     * @return void
     */
    public function prepareProductCollection($collection)
    {
        parent::prepareProductCollection($collection);

        // locale date is not used, because save does not use it too.
        // @see /Magento/Reports/Model/Product/Index/AbstractIndex::beforeSave
        $dateFrom = (new \DateTime())
            ->sub(new \DateInterval($this->getPeriod()))
            ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $collection->filterByPopularity(
            $this->getMinPopularity(),
            $this->getMaxPopularity()
        );
        $collection->getSelect()->where('order.created_at > ?', $dateFrom);
    }

    protected function initToolbar($toolbar)
    {
        parent::initToolbar($toolbar);
        if ($toolbar->getCurrentOrder() === 'popularity') {
            $toolbar->setSkipOrder(true);
            $this->getProductCollection()->getSelect()->order('popularity DESC');
        }
    }

    public function getDefaultSortField()
    {
        return 'popularity';
    }

    public function getDefaultSortFieldLabel()
    {
        return __('Popularity');
    }

    public function getDefaultSortDirection()
    {
        return 'DESC';
    }

    public function getPeriod()
    {
        if (!$this->hasData('period')) {
            return 'P1M'; // 1 month
        }
        return $this->getData('period');
    }

    public function getMinPopularity()
    {
        if (!$this->hasData('min_popularity')) {
            return 1;
        }
        return $this->getData('min_popularity');
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        if (false === $this->getIsWidget()) {
            return parent::getCacheKeyInfo();
        }

        $keyInfo = parent::getCacheKeyInfo();
        $keyInfo['period'] = $this->getPeriod();
        return $keyInfo;
    }
}
