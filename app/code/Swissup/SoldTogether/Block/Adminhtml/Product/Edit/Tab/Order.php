<?php
namespace Swissup\SoldTogether\Block\Adminhtml\Product\Edit\Tab;

class Order extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Related
{

    /**
     * @var \Swissup\SoldTogether\Model\AbstractModel
     */
    protected $_model;

    protected function _construct()
    {
        parent::_construct();
        $this->setId('soldtogether_order_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        $this->setDefaultFilter(['in_products' => 1]);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_model = $objectManager->get('Swissup\SoldTogether\Model\Order');
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $ids = $this->_getSelectedProducts();
            if (empty($ids)) {
                $ids = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $ids]);
            } else {
                if ($ids) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $ids]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $link = $this->_linkFactory->create()->useRelatedLinks();
        $collection = $link->getProductCollection();
        $collection->addAttributeToSelect('*');
        $ids = $this->_getSelectedProducts();
        $productId = $this->getRequest()->getParam('id');
        if (empty($ids)) {
            $ids = [0];
        }
        //$collection->addFieldToFilter('entity_id', ['in' => $productIds]);
        $collection->getSelect()
            ->joinLeft(
                ['so' => $this->_model->getResource()->getMainTable()],
                'so.related_id=e.entity_id and so.product_id='.$productId,
                ['soldtogether_weight' => 'so.weight']
            );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->removeColumn('position');

        $this->addColumn(
            'weight',
            [
                'header' => __('Weight'),
                'name' => 'weight',
                'type' => 'number',
                'editable' => true,
                'validate_class' => 'validate-number',
                'index' => 'soldtogether_weight',
                'header_css_class' => 'col-position',
                'column_css_class' => 'col-position'
            ]
        );

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl(
            'soldtogether/product/orderGrid',
            ['_current' => true]
        );
    }

    protected function _getSelectedProducts()
    {
        return array_keys($this->getSelectedRelatedProducts());
    }

    public function getSelectedRelatedProducts()
    {
        $productId  = $this->getRequest()->getParam('id');
        $relatedItems = $this->_model->getRelatedProductData($productId);
        $products = [];
        foreach ($relatedItems as $item) {
            $products[$item['related_id']] = [
                'weight' => $item['weight']
            ];
        }

        return $products;
    }
}
