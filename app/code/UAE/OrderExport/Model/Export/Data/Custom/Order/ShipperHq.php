<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace UAE\OrderExport\Model\Export\Data\Custom\Order;

use Xtento\OrderExport\Model\Export;

/**
 * Class extends fields exported by Xtento_OrderExport details.
 */
class ShipperHq extends \Xtento\OrderExport\Model\Export\Data\AbstractData
{
    /**
     * @var \ShipperHQ\Shipper\Model\ResourceModel\Order\Detail\CollectionFactory
     */
    private $orderDetailCollection;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Xtento\XtCore\Helper\Date $dateHelper
     * @param \Xtento\XtCore\Helper\Utils $utilsHelper
     * @param \ShipperHQ\Shipper\Model\ResourceModel\Order\Detail\CollectionFactory $orderDetailCollection
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Xtento\XtCore\Helper\Date $dateHelper,
        \Xtento\XtCore\Helper\Utils $utilsHelper,
        \ShipperHQ\Shipper\Model\ResourceModel\Order\Detail\CollectionFactory $orderDetailCollection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $dateHelper, $utilsHelper, $resource, $resourceCollection, $data);
        $this->orderDetailCollection = $orderDetailCollection;
    }

    /**
     * Retrieve configuration.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return [
            'name' => 'ShipperHQ',
            'category' => 'Order',
            'description' => 'Export ShipperHq fields',
            'enabled' => true,
            'apply_to' => [Export::ENTITY_ORDER],
            'third_party' => true,
            'depends_module' => 'ShipperHQ_Shipper',
        ];
    }

    /**
     * Add ShipperHQ order details 'liftgate_required' and 'inside_delivery' field values to order export.
     *
     * @param string $entityType
     * @param \Xtento\OrderExport\Model\Export\Entity\Collection\Item $collectionItem
     * @return array
     */
    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = [];
        $this->writeArray = & $returnArray['shipper_hq']; // Write on "shipper_hq" level

        if (!$this->fieldLoadingRequired('shipper_hq')) {
            return $returnArray;
        }

        // Fetch fields to export
        $order = $collectionItem->getOrder();
        try {
            $shipperOrderDetails = $this->orderDetailCollection
                ->create()
                ->addFieldToFilter('order_id', ['eq' => $order->getId()])
                ->addFieldToSelect(['liftgate_required', 'inside_delivery'])
                ->getFirstItem();
            if ($shipperOrderDetails->getId()) {
                foreach ($shipperOrderDetails->getData() as $key => $value) {
                    $this->writeValue($key, $value);
                }
            }
        } catch (\Exception $e) {

        }

        // Done
        return $returnArray;
    }
}
