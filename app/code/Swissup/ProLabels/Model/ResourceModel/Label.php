<?php

namespace Swissup\ProLabels\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\CatalogRule\Model\ResourceModel\Rule as Rule;
//use Magento\CatalogRule\Model\ResourceModel\Rule as Rule
/**
 * ProLabels Label mysql resource
 */
class Label extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('swissup_prolabels_label', 'label_id');
    }

    protected function _afterLoad(AbstractModel $object)
    {
        return $this;
    }

    protected function _afterSave(AbstractModel $object)
    {
        return $this;
    }

    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $rule)
    {
        return $this;
    }

    public function deleteAllIndexes()
    {
        try {
            $connection = $this->getConnection();
            $connection->delete(
                $this->getTable('swissup_prolabels_index')
            );
        } catch (\Exception $e) {
            return $this;
        }

        return $this;
    }

    public function addLabelIndexes($data)
    {
        try {
            $connection = $this->getConnection();
            $connection->insertMultiple(
                $this->getTable('swissup_prolabels_index'), $data);
        } catch (\Exception $e) {
            return $this;
        }

        return $this;
    }

    public function getIndexedProducts($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('swissup_prolabels_index'),
            'entity_id'
        )->where(
            'label_id = :label_id'
        );
        $binds = [':label_id' => (int)$id];
        return $connection->fetchCol($select, $binds);
    }

    public function getItemsToReindex($count, $step)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('catalog_product_entity'),
            'entity_id'
        )->order('entity_id')
        ->limit($count, $count * $step);

        return $connection->fetchCol($select);
    }

    public function validateProductSuperLink($ids)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('catalog_product_super_link'),
            ['product_id', 'parent_id']
        )->where('product_id IN (?)', $ids);
        $result = [];
        foreach ($this->getConnection()->fetchAll($select) as $superLink) {
            $result[$superLink['product_id']] = $superLink['parent_id'];
        }

        return $result;
    }

    public function getProductLabels($productId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('swissup_prolabels_index'),
            'label_id'
        )->where('entity_id = ?', $productId);

        return $connection->fetchCol($select);
    }

    public function getCatalogLabels($productIds)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('swissup_prolabels_index'),
            ['label_id', 'entity_id']
        )->where('entity_id IN (?)', $productIds);
        $indexData = $connection->fetchAll($select);
        $allLabelIds = [];
        foreach ($indexData as $item) {
            $allLabelIds[] = $item['label_id'];
        }

        $allLabelIds = array_unique($allLabelIds);

        $select = $connection->select()->from(
            $this->getTable('swissup_prolabels_label')
        )->where('label_id IN (?)', $allLabelIds)
         ->where('status = ?', 1);

        $labelSelectData = $connection->fetchAll($select);

        if (count($labelSelectData) === 0) {
            return [];
        }

        $labelData = [];
        foreach ($labelSelectData as $label) {
            $labelData[$label['label_id']] = $label;
        }
        $result = [];
        foreach ($indexData as $index) {
            if (isset($labelData[$index['label_id']])) {
                $result[$index['entity_id']][$index['label_id']] = $labelData[$index['label_id']];
            }
        }

        return $result;
    }
}
