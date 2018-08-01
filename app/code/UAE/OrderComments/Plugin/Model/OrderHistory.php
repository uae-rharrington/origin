<?php
/**
 * Shipping Information Management
 *
 * @category UAE
 * @package UAE_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace UAE\OrderComments\Plugin\Model;

use Magento\Sales\Model\Order;
use Magento\Framework\DataObjectFactory;

/**
 * UAE\OrderComments\Plugin\Model\ShippingInformationManagement
 *
 * @category UAE
 * @package UAE_OrderComments
 */
class OrderHistory
{
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * OrderHistory constructor.
     *
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(DataObjectFactory $dataObjectFactory)
    {
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Add order comment
     *
     * @param Order $subject
     * @param array $result
     * @return array $result
     */
    public function afterGetVisibleStatusHistory(
        Order $subject,
        array $result
    ){
        $orderComment = $subject->getOrderComment();
        if ($orderComment) {
            $dataObject = $this->dataObjectFactory->create();
            $dataObject->setComment($orderComment)
                ->setCreatedAt($subject->getCreatedAt());
            array_unshift($result, $dataObject);
        }

        return $result;
    }
}
