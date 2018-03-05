<?php

namespace Swissup\Highlight\Block\ProductList\Attribute;

class Date extends \Swissup\Highlight\Block\ProductList\All
{
    protected $widgetPageVarName = 'hdp';

    protected $widgetPriceSuffix = 'date';

    protected $widgetCssClass = 'highlight-date';

    /**
     * @param  \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return void
     */
    public function prepareProductCollection($collection)
    {
        parent::prepareProductCollection($collection);
        try {
            $attribute = $this->getAttributeCode();
            if (!is_array($attribute)) {
                $attribute = explode(',', $attribute);
            }

            $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

            if (!empty($attribute[0]) && !empty($attribute[1])) {
                $collection->addAttributeToFilter(
                    $attribute[0],
                    [
                        'or' => [
                            0 => ['date' => true, 'to' => $todayEndOfDayDate],
                            1 => ['is' => new \Zend_Db_Expr('null')],
                        ]
                    ],
                    'left'
                )
                ->addAttributeToFilter(
                    $attribute[1],
                    [
                        'or' => [
                            0 => ['date' => true, 'from' => $todayStartOfDayDate],
                            1 => ['is' => new \Zend_Db_Expr('null')],
                        ]
                    ],
                    'left'
                )
                ->addAttributeToFilter(
                    [
                        ['attribute' => $attribute[0], 'is' => new \Zend_Db_Expr('not null')],
                        ['attribute' => $attribute[1], 'is' => new \Zend_Db_Expr('not null')],
                    ]
                );
            } elseif (!empty($attribute[0])) {
                $collection->addAttributeToFilter(
                    $attribute[0],
                    [
                        'date' => true,
                        'to' => $todayEndOfDayDate
                    ]
                );
            } elseif (!empty($attribute[1])) {
                $collection->addAttributeToFilter(
                    $attribute[1],
                    [
                        'date' => true,
                        'from' => $todayStartOfDayDate
                    ]
                );
            }
        } catch (\Exception $e) {
            $this->setTemplate(null);
            $this->setCustomTemplate(null);
        }
    }

    public function getDefaultSortField()
    {
        $attribute = $this->getAttributeCode();
        if (!is_array($attribute)) {
            $attribute = explode(',', $attribute);
        }

        if (!empty($attribute[0])) {
            return $attribute[0];
        } elseif (!empty($attribute[1])) {
            return $attribute[1];
        }
    }

    public function getDefaultSortFieldLabel()
    {
        return __('Date');
    }

    public function getDefaultSortDirection()
    {
        return 'desc';
    }
}
