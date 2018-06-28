<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace UAE\GroupedProduct\Block\Product;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Product;

/**
 * Reviews block for simple products on the grouped product page.
 */
class Review extends Template implements IdentityInterface
{
    /**
     * @var Product
     */
    private $product;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * Review resource model
     *
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    private $reviewsColFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->reviewsColFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Set current simple product and title for reviews tab.
     *
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        $this->setTabTitle();

        return $this;
    }

    /**
     * Get current simple product.
     *
     * @return Product|null
     */
    public function getProduct()
    {
        return $this->product ? $this->product : null;
    }

    /**
     * Get current product id.
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->getProduct();

        return $product ? $product->getId() : null;
    }

    /**
     * Get URL for ajax call.
     *
     * @return string
     */
    public function getProductReviewUrl()
    {
        return $this->getUrl(
            'review/product/listAjax',
            [
                '_secure' => $this->getRequest()->isSecure(),
                'id' => $this->getProductId(),
            ]
        );
    }

    /**
     * Set tab title.
     *
     * @return void
     */
    public function setTabTitle()
    {
        $title = $this->getCollectionSize()
            ? __('Reviews %1', '<span class="counter">' . $this->getCollectionSize() . '</span>')
            : __('Reviews');
        $this->setTitle($title);
    }

    /**
     * Get size of reviews collection.
     *
     * @return int
     */
    public function getCollectionSize()
    {
        $collection = $this->reviewsColFactory->create()->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addStatusFilter(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $this->getProductId()
        );

        return $collection->getSize();
    }

    /**
     * Return unique ID(s) for each object in system.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Review\Model\Review::CACHE_TAG . $this->getProductId()];
    }
}
