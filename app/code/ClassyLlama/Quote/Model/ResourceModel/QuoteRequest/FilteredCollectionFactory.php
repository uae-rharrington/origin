<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Model\ResourceModel\QuoteRequest;

class FilteredCollectionFactory implements \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    private $instanceName = null;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = '\\ClassyLlama\\Quote\\Model\\ResourceModel\\QuoteRequest\\FilteredCollection'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * @inheritdoc
     */
    public function create($customerId = null)
    {
        /** @var \ClassyLlama\Quote\Model\ResourceModel\QuoteRequest\FilteredCollection $collection */
        $collection = $this->objectManager->create($this->instanceName);

        if ($customerId) {
            $collection->addFieldToFilter('customer_id', $customerId);
        }

        return $collection;
    }
}
