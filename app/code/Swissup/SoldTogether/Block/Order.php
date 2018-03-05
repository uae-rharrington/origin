<?php

namespace Swissup\SoldTogether\Block;

use Magento\Customer\Model\Context;

class Order extends \Magento\Catalog\Block\Product\AbstractProduct
    implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Can be 'order' or 'customer'
     */
    const SOLDTOGETHER_ENTITY = 'order';

    /**
     * Name of table in DB
     *
     * @var string
     */
    protected $_tableName = 'swissup_soldtogether_order';

    /**
     *  Product collection
     *
     * @var Collection
     */
    protected $_collection;

    /**
     * @var \Swissup\SoldTogether\Helper\Stock
     */
    protected $stockHelper;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Block\Product\Context    $context
     * @param \Magento\Framework\Module\Manager         $moduleManager
     * @param \Magento\Catalog\Model\Product\Visibility $catalogVisibility
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Product\Visibility $catalogVisibility,
        \Swissup\SoldTogether\Helper\Stock $stockHelper,
        array $data = []
    ) {
        $this->_catalogProductVisibility = $catalogVisibility;
        $this->moduleManager = $moduleManager;
        $this->stockHelper = $stockHelper;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Initialize block's cache
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData(
            [
                'cache_lifetime' => 86400,
                'cache_tags' => [\Magento\Catalog\Model\Product::CACHE_TAG]
            ]
        );
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $httpContext = $_objectManager->get('Magento\Framework\App\Http\Context');
        $product = $this->_coreRegistry->registry('product');

        return [
            'SOLDTOGETHER_' . static::SOLDTOGETHER_ENTITY,
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $httpContext->getValue(Context::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            $this->getConfig('count'),
            $product ? $product->getId() : null
        ];
    }

    /**
     * Prepare product collection using soldtogether data
     *
     * @return $this
     */
    protected function _prepareSoldTogetherData()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $this->_coreRegistry->registry('product');

        if (!$this->getConfig('enabled') || !$product) {
            return $this;
        }

        /* @var $product \Magento\Catalog\Model\Product */
        $productCollection = $_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $resource = $_objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->_collection = $productCollection->addAttributeToSelect(
            'required_options'
        )->addStoreFilter();

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_collection);
        }
        $this->_collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $this->_collection->getSelect()
            ->joinInner(
                ['sc' => $resource->getTableName($this->_tableName)],
                'sc.related_id=e.entity_id and sc.product_id=' . $product->getId(),
                ['soldtogether_weight' => 'sc.weight']
            );

        $this->_collection->getSelect()->order('soldtogether_weight ' . \Magento\Framework\DB\Select::SQL_DESC);
        if (!$this->getConfig('options')) {
            // do not show products with options (configurable, grouped)
            // only 'customer also bought' has this option
            $this->_collection->getSelect()
                ->where('e.type_id IN (?)', ['simple', 'virtual']);
        }

        if (!$this->getConfig('out')) {
            // show out of stock products disabled
            $this->stockHelper->addInStockFilterToCollection($this->_collection);
        }

        $this->_collection->getSelect()->limit($this->getConfig('count'));

        if ($this->_collection->count() === 0 && $this->getConfig('random')) {
            $this->getRandomCollection($product);
        }

        return $this;
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareSoldTogetherData();
        return parent::_beforeToHtml();
    }

    /**
     * Prepare random collection of products from same category
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function getRandomCollection($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($product->hasCategory()) {
            $category = $product->getCategory();
        } elseif ($product->hasCategoryIds()) {
            $categoryIds = $product->getCategoryIds();
            $category = $objectManager->get('Magento\Catalog\Model\Category');
            $category->load(reset($categoryIds));
        } else {
            return $this;
        }


        $this->_collection = $category->getProductCollection();

        $this->_collection->addAttributeToSelect(
            'required_options'
        )->addStoreFilter();

        $this->_addProductAttributesAndPrices($this->_collection);
        $this->_collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $this->_collection->addAttributeToFilter('entity_id', ['nin' => [$product->getId()]]);

        if (!$this->getConfig('options')) {
            // do not show products with options (configurable, grouped)
            // only 'customer also bought' has this option
            $this->_collection->getSelect()
                ->where('e.type_id IN (?)', ['simple', 'virtual']);
        }

        if (!$this->getConfig('out')) {
            // display out of stock products disabled
            $this->stockHelper->addInStockFilterToCollection($this->_collection);
        }

        $this->_collection->getSelect()->order('rand()');
        $this->_collection->getSelect()->limit($this->getConfig('count'));

        return $this;
    }

    /**
     * Get price format
     *
     * @return array
     */
    public function getPriceFormat()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $localeFormat = $objectManager->get('Magento\Framework\Locale\FormatInterface');

        return $localeFormat->getPriceFormat();
    }

    /**
     * Get collection items
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getItems()
    {
        return $this->_collection;
    }

    /**
     * Get tax display config
     *
     * @return string
     */
    public function getTaxDisplayConfig()
    {
        return $this->_scopeConfig->getValue(
            "tax/display/type",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get IDs of products
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($this->getItems()) {
            foreach ($this->getItems() as $item) {
                $identities = array_merge($identities, $item->getIdentities());
            }
        }

        return $identities;
    }

    /**
     * Get config value
     *
     * @param  string $key
     * @return string
     */
    public function getConfig($key)
    {
        return $this->_scopeConfig->getValue(
            sprintf("soldtogether/%s/%s", static::SOLDTOGETHER_ENTITY, $key),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
