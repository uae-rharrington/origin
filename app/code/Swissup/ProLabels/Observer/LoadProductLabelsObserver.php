<?php
namespace Swissup\ProLabels\Observer;

use Magento\Framework\Event\ObserverInterface;

class LoadProductLabelsObserver implements ObserverInterface
{
    /**
     * Add labels data for catalog product collection
     * only for front end
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();

        /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $allIds = $collection->getAllIds();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $labelModel = $objectManager->get('Swissup\ProLabels\Model\Label');
        $catalogLabels = $labelModel->getCatalogLabels($allIds);
        foreach ($collection as $product) {
            if (array_key_exists($product->getId(), $catalogLabels)) {
                $product->setData('product_labels', $catalogLabels[$product->getId()]);
            }
        }

        return $this;
    }
}
