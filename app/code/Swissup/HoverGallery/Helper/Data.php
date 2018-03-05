<?php
namespace Swissup\HoverGallery\Helper;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Path to store config if frontend output is enabled
     *
     * @var string
     */
    const XML_PATH_ENABLED = 'hovergallery/general/enabled';

    protected function getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Checks whether hover gallery can be displayed in the frontend
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool)$this->getConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Catalog\Model\Product\Gallery\ReadHandler $galleryReadHandler
     */
    public function __construct(
        Context $context,
        GalleryReadHandler $galleryReadHandler,
        ImageHelper $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
        $this->galleryReadHandler = $galleryReadHandler;
        parent::__construct($context);
    }

    /**
     * Add media gallery to product
     *
     * @param \Magento\Catalog\Model\Product $product
     */
    public function addGallery($product)
    {
        $this->galleryReadHandler->execute($product);
    }

    /**
     * Get product hover image html
     * @param \Magento\Catalog\Model\Product $product
     * @param string $width
     * @param string $height
     * @param bool $keepFrame
     * @return string
     */
    public function getHoverImage($product, $width, $height, $keepFrame = true)
    {
        if (!$this->isEnabled()) {
            return '';
        }

        if ($img = $product->getHoverImage()) {
            $imageUrl = $this->imageHelper
                ->init($product, 'hover_image')
                ->setImageFile($img['file'])
                ->keepFrame($keepFrame)
                ->resize($width, $height)
                ->getUrl();
            return sprintf(
                '<img src="%s"
                    srcset="%s"
                    class="hover-image"
                    width="%s"
                    height="%s"
                    alt="%s" />',
                $imageUrl,
                $imageUrl,
                $width,
                $height,
                $product->getName()
            );
        }
    }
}
