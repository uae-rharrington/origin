<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Model\ResourceModel\Order;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Collection
{
    /**
     * @var array
     */
    private $baseFilters;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\DB\Helper $coreResourceHelper
     * @param null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     * @param array $baseFilters
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DB\Helper $coreResourceHelper,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        array $baseFilters = []
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $entitySnapshot, $coreResourceHelper, $connection, $resource);

        $this->baseFilters = $baseFilters;
    }

    /**
     * Before load action
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        foreach ($this->baseFilters as $fieldKey => $filter) {
            $this->addAttributeToFilter($fieldKey, $filter);
        }
        parent::_beforeLoad();

        return $this;
    }
}
