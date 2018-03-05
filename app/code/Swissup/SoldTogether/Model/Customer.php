<?php

namespace Swissup\SoldTogether\Model;

class Customer extends Order
{
    /**
     * @var string
     */
    protected $_cacheTag = 'soldtogether_Customer';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'soldtogether_Customer';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Swissup\SoldTogether\Model\ResourceModel\Customer');
    }

    public function getCustomerOrderIds($count, $step)
    {
        return $this->_getResource()->getCustomerOrderIds($count, $step);
    }

    public function createNewRelations($order)
    {
        $customerEmail = $order->getCustomerEmail();
        $storeId = $order->getStoreId();
        $orderId = $order->getId();
        $productIds = $this->_getResource()->getCustomerNewOrderIds($customerEmail, $orderId, $storeId);

        $result = [];
        $visibleItems = $order->getAllVisibleItems();

        foreach ($visibleItems as $product) {
            foreach ($productIds as $relatedId => $relatedData) {
                if ($product->getProductId() == $relatedId) {
                    continue;
                }
                $result[] = [
                    'product_id'   => $product->getProductId(),
                    'related_id'   => $relatedId,
                    'product_name' => $product->getName(),
                    'related_name' => $relatedData['name'],
                    'store_id'     => 0,
                    'weight'       => 1,
                    'is_admin'     => 0
                ];
            }
        }
        if (count($result) > 0) {
            // add data to db
            $dbData = [];
            foreach ($result as $item) {
                $itemExist = $this->relationExist($item['product_id'], $item['related_id'], 0);
                if ($itemExist) {
                    try {
                        $this->load($itemExist);
                        if (!(int)$this->getIsAdmin()) {
                            $this->setWeight($this->getWeight() + 1);
                            $this->save();
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                } else {
                    $dbData[] = $item;
                }
            }
            if (count($dbData) > 0) {
                try {
                    $connection = $this->_getResource()->getConnection();
                    $connection->insertMultiple(
                        $this->_getResource()->getTable('swissup_soldtogether_customer'),
                        $dbData
                    );
                } catch (\Exception $e) {
                }
            }
        }
    }
}
