<?php
namespace Swissup\HoverGallery\Observer;

use Magento\Framework\Data\Collection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Swissup\HoverGallery\Helper\Data as DataHelper;

class AppendMediaGalleryBeforeHtml implements ObserverInterface
{
    /**
     * Constructor
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(DataHelper $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }
    /**
     * Append media gallery before rendering html
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Swissup\HoverGallery\Observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->dataHelper->isEnabled()) {
            return $this;
        }

        $productCollection = $observer->getEvent()->getCollection();

        if ($productCollection instanceof Collection) {
            $productCollection->load();

            foreach ($productCollection as $product) {
                $this->dataHelper->addGallery($product);
                $mediaGallery = $product->getMediaGalleryImages();

                if ($mediaGallery && $img = $mediaGallery->getItemByColumnValue('position', '2')) {
                    $product->setHoverImage($img);
                }
            }
        }

        return $this;
    }
}
