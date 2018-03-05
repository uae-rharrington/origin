<?php
namespace Swissup\SoldTogether\Controller\Adminhtml\Customer;

class Reindex extends \Swissup\SoldTogether\Controller\Adminhtml\Order\Reindex
{
    /**
     * Prefix for data in backend session
     *
     * @var string
     */
    protected $_dataPrefix = "swissup_soldtogether_customer";
    /**
     * Size of step in data processing
     *
     * @var integer
     */
    protected $_stepSize = 5;
    /**
     * Message on processing complete
     *
     * @var string
     */
    protected $_completeMessage = "All Customers have been indexed.";

    protected function getModel()
    {
        return $this->_objectManager->create('Swissup\SoldTogether\Model\Customer');
    }

    protected function getProcessItemsCount()
    {
        return $this->_objectManager
            ->get('Magento\Customer\Model\ResourceModel\Customer\Collection')
            ->getSize();
    }

    protected function processStep($step, $stepSize)
    {
        $result = [];
        $productIds = $this->getModel()->getCustomerOrderIds($stepSize, $step);
        foreach ($productIds as $productId => $orderData) {
            foreach ($productIds as $relatedId => $relatedData) {
                if ($productId == $relatedId) {
                    continue;
                }
                if ($orderData['store'] != $relatedData['store']) {
                    continue;
                }
                $result[] = [
                    'product_id'   => $productId,
                    'related_id'   => $relatedId,
                    'product_name' => $orderData['name'],
                    'related_name' => $relatedData['name'],
                    'store_id'     => 0,
                    'weight'       => 1,
                    'is_admin'     => 0
                ];
            }
        }

        return $result;

    }

}
