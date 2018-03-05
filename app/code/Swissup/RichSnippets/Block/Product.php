<?php

namespace Swissup\RichSnippets\Block;

use Magento\Framework\View\Element\Template;
use Swissup\RichSnippets\Model\Config\Source\StructuredDataFormat;

class Product extends LdJson
{
    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Template\Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_imageHelper = $imageHelper;
        $this->_pricingHelper = $pricingHelper;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $dataFormat = $this->getStoreConfig('richsnippets/general/product_format');
        if ($dataFormat != StructuredDataFormat::MICRODATA) {
            // unset microdata attributes added in Magento_Catalog module
            $this->unsetLayoutBlockData('page.main.title', 'add_base_attribute')
                ->unsetLayoutBlockData('product.info.sku', 'add_attribute')
                ->unsetLayoutBlockData('product.info.overview', 'add_attribute');
            // Remove itemtype and itemscope attributes from body.
            // We have to use reflection because there is no method or layout
            // instruction for this
            $refProperty = new \ReflectionProperty($this->pageConfig, 'elements');
            $refProperty->setAccessible(true);
            $attributes = $refProperty->getValue($this->pageConfig);
            unset($attributes['body']['itemtype']);
            unset($attributes['body']['itemscope']);
            $refProperty->setValue($this->pageConfig, $attributes);
        }

        return parent::_prepareLayout();
    }

    /**
     * Unset data with $dataKey for block with name $blockName
     *
     * @param  string $blockName
     * @param  string $dataKey
     * @return $this
     */
    private function unsetLayoutBlockData($blockName, $dataKey)
    {
        if ($block = $this->getLayout()->getBlock($blockName)) {
            $block->unsetData($dataKey);
        }

        return $this;
    }

    /**
     * Get current product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     * {@inheritdoc}
     */
    public function getLdJson()
    {
        $product = $this->getProduct();
        $dataFormat = $this->getStoreConfig('richsnippets/general/product_format');
        if (!$product || $dataFormat != StructuredDataFormat::JSON_LD) {
            // product not found
            // or structured data format is not JSON-LD
            return '';
        }

        $store = $this->_storeManager->getStore();
        $image = $this->_imageHelper
            ->init($product, 'product_page_image_small')
            ->setImageFile($product->getImage())
            ->getUrl();

        $data = array(
            '@context'              => 'http://schema.org',
            '@type'                 => 'Product',
            'name'                  => $product->getName(),
            'image'                 => $image,
            'description'           => $product->getShortDescription(),
            'sku'                   => $product->getSku(),
            'offers'                => array(
                '@type'             => 'Offer',
                'availability'      => $this->getStockStatusUrl(),
                'priceCurrency'     => $store->getCurrentCurrency()->getCode(),
                'itemCondition'     => 'http://schema.org/NewCondition'
            )
        );
        if (is_array($this->getPriceValues())) {
            $getPriceValues = $this->getPriceValues();
            $minPrice = $this->_pricingHelper->currencyByStore(min($getPriceValues), $store, false, false);
            $maxPrice = $this->_pricingHelper->currencyByStore(max($getPriceValues), $store, false, false);
            $data['offers']['@type'] = 'AggregateOffer';
            $data['offers']['lowPrice'] = $minPrice;
            $data['offers']['highPrice'] = $maxPrice;
        } else {
            $price = $this->_pricingHelper->currencyByStore($this->getPriceValues(), $store, false, false);
            $data['offers']['price'] = $price;
        }

        $review = $this->_objectManager->get('Magento\Review\Model\Review\Summary');
        $summaryData = $review->setStoreId($store->getId())->load($product->getId());
        if ((int)$summaryData->getReviewsCount() > 0) {
            $data['aggregateRating']['@type'] = 'AggregateRating';
            $data['aggregateRating']['bestRating'] = '100';
            $data['aggregateRating']['worstRating'] = '0';
            $data['aggregateRating']['ratingValue'] = $summaryData->getRatingSummary();
            $data['aggregateRating']['reviewCount'] = $summaryData->getReviewsCount();
            $data['aggregateRating']['ratingCount'] = $summaryData->getReviewsCount();
        }

        return $this->prepareJsonString($data);
    }

    /**
     * Get stock status URL
     *
     * @return string
     */
    public function getStockStatusUrl()
    {
        if ($this->getProduct()->isSaleable()){
            $availability = 'http://schema.org/InStock';
        } else {
            $availability = 'http://schema.org/OutOfStock';
        }
        return $availability;
    }

    /**
     * @return mixed Array with min and max values or float
     */
    public function getPriceValues()
    {
        $product     = $this->getProduct();
        $priceModel  = $product->getPriceModel();
        $productType = $product->getTypeInstance();
        if ('bundle' === $product->getTypeId()) {
            return $priceModel->getTotalPrices($product);
        }

        if ('grouped' === $product->getTypeId()) {
            $assocProducts = $productType->getAssociatedProductCollection($product)
                ->addMinimalPrice()
                ->setOrder('minimal_price', 'ASC');

            foreach ($assocProducts as $assocProduct) {
                $groupedProductsPricesArray[] = $assocProduct->getFinalPrice();
            }

            return $groupedProductsPricesArray;
        }

        $minPrice   = $product->getMinimalPrice();
        $finalPrice = $product->getFinalPrice();
        if ($minPrice && $minPrice < $finalPrice) {
            return array($minPrice, $finalPrice);
        }

        return $finalPrice;
    }
}
