<?php
/**
 * Apply Quote Observer
 *
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
namespace UAE\QuoteCustom\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\ResourceConnection;

/**
 * UAE\QuoteCustom\Observer\ApplyQuote
 *
 * @category UAE
 * @package UAE_QuoteCustom
 */
class ApplyQuote implements ObserverInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * ApplyQuote constructor.
     *
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ){
       $this->resource = $resource;
    }

    /**
     * Apply quote to registered customer
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $customerEmail = $customer->getEmail();
        $customerId = $customer->getId();

        /** It can't be done using order collection because of
         *  ClassyLlama\Quote\Model\ResourceModel\Order\Collection
         *  _beforeLoad() always set condition `is_quote_request` = '0'
         */
        $this->resource->getConnection()->update(
            $this->resource->getTableName('sales_order'),
            ['customer_id' => $customerId],
            [
                'customer_email = ?' => $customerEmail,
                'is_quote_request = 1',
            ]
        );
    }
}
