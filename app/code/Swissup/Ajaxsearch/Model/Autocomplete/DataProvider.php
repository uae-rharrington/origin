<?php
namespace Swissup\Ajaxsearch\Model\Autocomplete;

use Swissup\Ajaxsearch\Model\Query\Autocomplete as Query;
use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Swissup\Ajaxsearch\Model\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider implements DataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $enable = $this->configHelper->isAutocompleteEnabled();
        if (!$enable) {
            return [];
        }

        $this->queryFactory->setInstanceName(Query::class);
        $collection = $this->getSuggestCollection();
        $limit = $this->configHelper->getAutocompleteLimit();
        if ($limit) {
            $collection->setPageSize($limit);
        }

        $queryText = $this->getQuery()->getQueryText();
        $result = [];
        foreach ($collection as $item) {
            $resultItem = $this->itemFactory->create([
                'title' => $item->getQueryText(),
                'num_results' => $item->getNumResults(),
            ]);
            if ($resultItem->getTitle() == $queryText) {
                array_unshift($result, $resultItem);
            } else {
                $result[] = $resultItem;
            }
        }
        return $result;
    }
}
