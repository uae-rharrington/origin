<?php
namespace Swissup\Lightboxpro\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Json\EncoderInterface;
use Swissup\Lightboxpro\Model\Config\Source\PopupLayouts;
use Swissup\Lightboxpro\Model\Config\Source\ThumbnailsTypes;

class Config extends AbstractHelper
{
    /**
     * Width of thumbnails panel in advanced popup
     */
    const ADVANCED_POPUP_THUMBS_PANEL_WIDTH = 230;

    /**
     * Path to store config is zoom feature enabled
     */
    const ZOOM_ENABLED = 'lightboxpro/general/enable_zoom';

    /**
     * Path to store config is popup enabled
     */
    const POPUP_ENABLED = 'lightboxpro/general/enable_popup';

    /**
     * Path to store config thumbnails type
     */
    const THUMBNAILS_TYPE = 'lightboxpro/general/thumbnails';

    /**
     * Path to store config main image width
     */
    const MAIN_IMG_WIDTH = 'lightboxpro/size/image_width';

    /**
     * Path to store config main image height
     */
    const MAIN_IMG_HEIGHT = 'lightboxpro/size/image_height';

    /**
     * Path to store config thumbnail width
     */
    const THUMBNAIL_WIDTH = 'lightboxpro/size/thumbnail_width';

    /**
     * Path to store config thumbnail height
     */
    const THUMBNAIL_HEIGHT = 'lightboxpro/size/thumbnail_height';

    /**
     * Path to store config popup layout type
     */
    const POPUP_TYPE = 'lightboxpro/popup/type';

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Catalog\Block\Product\View\Gallery
     */
    protected $block;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Framework\Config\View
     */
    protected $configView;

    /**
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\ConfigInterface $viewConfig
    ) {
        parent::__construct($context);

        $this->jsonEncoder = $jsonEncoder;
        $this->imageHelper = $imageHelper;
        $this->configView = $viewConfig->getViewConfig();
    }

    public function init($block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Check if zoom feature is enabled
     * @return boolean
     */
    public function zoomEnabled()
    {
        return (bool)$this->getConfig(self::ZOOM_ENABLED);
    }

    public function getPopupEnabled()
    {
        return $this->getConfig(self::POPUP_ENABLED) ? 'true' : 'false';
    }

    public function getMainImageWidth($noframe = false)
    {
        $type = 'product_page_image_medium' . ($noframe ? '_no_frame' : '');

        return (int)$this->getConfig(self::MAIN_IMG_WIDTH) ?:
            $this->getImageAttribute($type, 'width');
    }

    public function getMainImageHeight($noframe = false)
    {
        $type = 'product_page_image_medium' . ($noframe ? '_no_frame' : '');

        return (int)$this->getConfig(self::MAIN_IMG_HEIGHT) ?:
            $this->getImageAttribute($type, 'height');
    }

    public function getThumbnailWidth()
    {
        return (int)$this->getConfig(self::THUMBNAIL_WIDTH) ?:
            $this->getImageAttribute('product_page_image_small', 'width');
    }

    public function getThumbnailHeight()
    {
        return (int)$this->getConfig(self::THUMBNAIL_HEIGHT) ?:
            $this->getImageAttribute('product_page_image_small', 'height');
    }

    public function getMagnifierJson()
    {
        $magnifierConfig = $this->block->getVar('magnifier');
        $magnifierConfig['enabled'] = $this->zoomEnabled() ? 'true' : 'false';

        return $this->jsonEncoder->encode($magnifierConfig);
    }

    public function getNav()
    {
        $type = $this->getConfig(self::THUMBNAILS_TYPE);

        return $type == ThumbnailsTypes::TYPE_HIDDEN ? 'false' :
            $this->block->getVar("gallery/nav");
    }

    public function getNavDir()
    {
        $type = $this->getConfig(self::THUMBNAILS_TYPE);

        return ($type == ThumbnailsTypes::TYPE_HIDDEN ||
            $type == ThumbnailsTypes::TYPE_THEME) ?
            $this->block->getVar("gallery/navdir") : $type;
    }

    public function showFullscreenNav()
    {
        return ($this->getPopupLayoutType() != PopupLayouts::TYPE_SIMPLE);
    }

    public function getPopupLayoutType()
    {
        return $this->getConfig(self::POPUP_TYPE);
    }

    public function getFullscreenNavArrows()
    {
        return $this->isAdvancedPopup() ? 'false' :
            $this->block->getVar("gallery/fullscreen/navarrows");
    }

    public function getFullscreenArrows()
    {
        return $this->isAdvancedPopup() ? 'false' :
            $this->block->getVar("gallery/fullscreen/arrows");
    }

    public function getFullscreenNavDir()
    {
        return $this->isAdvancedPopup() ? 'vertical' :
            $this->block->getVar("gallery/fullscreen/navdir");
    }

    public function isAdvancedPopup()
    {
        return $this->getPopupLayoutType() == PopupLayouts::TYPE_ADVANCED;
    }

    public function getAdvancedLayoutThumbWidth()
    {
        return self::ADVANCED_POPUP_THUMBS_PANEL_WIDTH;
    }

    /**
     * Rewritten getGalleryImages method to change image sizes
     * @param  \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Framework\Data\Collection
     */
    public function getGalleryImages(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $images = $product->getMediaGalleryImages();
        $mainImageWidth = $this->getMainImageWidth(true);
        $mainImageHeight = $this->getMainImageHeight(true);
        $thumbnailWidth = $this->getThumbnailWidth();
        $thumbnailHeight = $this->getThumbnailHeight();

        if ($images instanceof \Magento\Framework\Data\Collection) {
            foreach ($images as $image) {
                $image->setData(
                    'small_image_url',
                    $this->imageHelper->init(
                            $product,
                            'product_page_image_small',
                            ['width' => $thumbnailWidth, 'height' => $thumbnailHeight]
                        )
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
                $image->setData(
                    'medium_image_url',
                    $this->imageHelper->init(
                            $product,
                            'product_page_image_medium_no_frame',
                            ['width' => $mainImageWidth, 'height' => $mainImageHeight]
                        )
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
                $image->setData(
                    'large_image_url',
                    $this->imageHelper->init($product, 'product_page_image_large_no_frame')
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
            }
        }

        return $images;
    }

    public function getBreakpoints()
    {
        $breakpointsConfig = $this->block->getVar('breakpoints');
        $breakpointsConfig['mobile']['options']['options']['allowfullscreen'] = true;

        return $this->jsonEncoder->encode($breakpointsConfig);
    }

    /**
     * Get store config by key
     * @param  string $key
     * @return mixed
     */
    protected function getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param string $imageId
     * @param string $attributeName
     * @param string $default
     * @return string
     */
    private function getImageAttribute($imageId, $attributeName, $default = null)
    {
        $attributes = $this->configView->getMediaAttributes(
            'Magento_Catalog',
            \Magento\Catalog\Helper\Image::MEDIA_TYPE_CONFIG_NODE,
            $imageId
        );

        return isset($attributes[$attributeName]) ? $attributes[$attributeName] : $default;
    }
}
