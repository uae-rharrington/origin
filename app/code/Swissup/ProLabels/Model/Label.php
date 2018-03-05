<?php
namespace Swissup\ProLabels\Model;

use Swissup\ProLabels\Api\Data\LabelInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Label  extends \Magento\Rule\Model\AbstractModel implements LabelInterface, IdentityInterface
{
    public $_superIds = [];

    public $_productsFilter = null;

    public $_productIds;

    /**
     * cache tag
     */
    const CACHE_TAG = 'prolabels_label';

    /**
     * @var string
     */
    protected $_cacheTag = 'prolabels_label';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'prolabels_label';

    protected $_eventObject = 'label';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $combineFactory,
        \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogRule\Helper\Data $catalogRuleData,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypesList,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor $ruleProductProcessor,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        $this->_productFactory = $productFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->_customerSession = $customerSession;
        $this->_catalogRuleData = $catalogRuleData;
        $this->_cacheTypesList = $cacheTypesList;
        $this->dateTime = $dateTime;
        $this->_ruleProductProcessor = $ruleProductProcessor;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );

        // Magento 2.2 compatibility with 2.1 support
        // @todo: switch to Json serializer, when clients will have 2.2 and higher
        // @see app/code/Magento/CatalogRule/Setup/UpgradeData.php
        if (@class_exists('\Magento\Framework\Serialize\Serializer\Serialize')) {
            $this->serializer = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Serialize\Serializer\Serialize::class
            );
        }
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Swissup\ProLabels\Model\ResourceModel\Label');
        $this->setIdFieldName('label_id');
    }

    public function beforeSave()
    {
        if ($this->getConditions()) {
            $this->setConditionsSerialized(serialize($this->getConditions()->asArray()));
            $this->_conditions = null;
        }
        if ($this->hasStores()) {
            $this->setStores(serialize($this->getStores()));
        }
        if ($this->hasCustomerGroupIds()) {
            $this->setCustomerGroupIds(serialize($this->getCustomerGroupIds()));
        }

        return $this;
    }

    public function afterSave()
    {
        return $this;
    }

    public function afterDelete()
    {
        return $this;
    }

    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    /**
     * Get array of product ids which are matched by label conditions
     *
     * @return array
     */
    public function getMatchingProductIds($productIds = [])
    {
        $this->_productIds = [];
        $this->setCollectedAttributes([]);

        $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addIdFilter($productIds);

        $this->getConditions()->collectValidatedAttributes($productCollection);
        $this->_resourceIterator->walk(
            $productCollection->getSelect(),
            [[$this, 'callbackValidateProduct']],
            [
                'attributes' => $this->getCollectedAttributes(),
                'product' => $this->_productFactory->create()
            ]
        );

        $this->_productIds = array_unique($this->_productIds);
        $result = $this->validateProductSuperLink($this->_productIds);
        $applyedProducts = $this->getIndexedProducts($this->getLabelId());
        $validateData = [];
        $notVisibleProducts = [];
        $notVisibleParent = [];
        if (count($result) > 0) {
            foreach ($result as $productId => $parentId) {
                $notVisibleProducts[] = $productId;
                if (in_array($parentId, $applyedProducts)) {
                    continue;
                } else {
                    if (!in_array($parentId, $notVisibleParent)) {
                        $validateData[] = array(
                            "label_id"  => $this->getLabelId(),
                            "entity_id" => $parentId
                        );
                        $notVisibleParent[] = $parentId;
                    }
                }
            }
            foreach ($this->_productIds as $productId) {
                if (!in_array($productId, $notVisibleProducts)) {
                    $validateData[] = array(
                        "label_id"  => $this->getLabelId(),
                        "entity_id" => $productId
                    );
                }
            }
        } else {
            foreach ($this->_productIds as $productId) {
                $validateData[] = array(
                    "label_id"  => $this->getLabelId(),
                    "entity_id" => $productId
                );
            }
        }

        return $validateData;
    }

    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        $storeIds = $this->getStoreId();
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        $results = [];
        foreach ($storeIds as $storeId) {
            $product->setStoreId($storeId);
            if ($result = $this->getConditions()->validate($product)) {
                $this->_productIds[] = $product->getId();
            }
        }
    }

    public function validateProductSuperLink($productIds)
    {
        return $this->_getResource()->validateProductSuperLink($productIds);
    }

    public function prepareProductsToIndexing()
    {
        $productCollection = $this->_productCollectionFactory->create();
        $allProductIds = $productCollection->getAllIds();

        return $allProductIds;
    }

    public function getIndexedProducts($id)
    {
        return $this->_getResource()->getIndexedProducts($id);
    }

    public function getItemsToReindex($count, $step)
    {
        return $this->_getResource()->getItemsToReindex($count, $step);
    }

    public function getProductLabels($productId)
    {
        return $this->_getResource()->getProductLabels($productId);
    }

    public function getCatalogLabels($productIds)
    {
        return $this->_getResource()->getCatalogLabels($productIds);
    }

    /**
     * Reindex All Labels By Cron
     *
     * @return boolean
     */
    public function reindexAll()
    {
        $this->deleteAllIndexes();
        $this->applyAllLabelsByCron();
        return true;
    }

    public function deleteAllIndexes()
    {
        return $this->_getResource()->deleteAllIndexes();
    }

    public function applyAllLabelsByCron()
    {
        $allLabels = $this->getCollection();
        foreach ($allLabels as $label) {
            $matchingData = [];
            $matchingProducts = $label->getMatchingProductIds();
            if (count($matchingProducts) > 0) {
                $this->addLabelIndexes($matchingProducts);
            }
        }
        return $this;
    }

    public function addLabelIndexes($data)
    {
        return $this->_getResource()->addLabelIndexes($data);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get label_id
     *
     * return int
     */
    public function getLabelId()
    {
        return $this->getData(self::LABEL_ID);
    }

    /**
     * Get title
     *
     * return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get stores
     *
     * return string
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get status
     *
     * return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Get Customer Groups
     *
     * return int
     */
    public function getCustomerGroups()
    {
        return $this->getData(self::CUSTOMER_GROUPS);
    }

    /**
     * Get condition_data
     *
     * return string
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * Get product_position
     *
     * return string
     */
    public function getProductPosition()
    {
        return $this->getData(self::PRODUCT_POSITION);
    }

    /**
     * Get product_image
     *
     * return string
     */
    public function getProductImage()
    {
        return $this->getData(self::PRODUCT_IMAGE);
    }

    /**
     * Get product_image_width
     *
     * return string
     */
    public function getProductImageWidth()
    {
        return $this->getData(self::PRODUCT_IMAGE_WIDTH);
    }

    /**
     * Get product_image_height
     *
     * return string
     */
    public function getProductImageHeight()
    {
        return $this->getData(self::PRODUCT_IMAGE_HEIGHT);
    }

    /**
     * Get product_custom_style
     *
     * return string
     */
    public function getProductCustomStyle()
    {
        return $this->getData(self::PRODUCT_CUSTOM_STYLE);
    }

    /**
     * Get product_text
     *
     * return string
     */
    public function getProductText()
    {
        return $this->getData(self::PRODUCT_TEXT);
    }

    /**
     * Get product_custom_url
     *
     * return string
     */
    public function getProductCustomUrl()
    {
        return $this->getData(self::PRODUCT_CUSTOM_URL);
    }

    /**
     * Get product_round_method
     *
     * return string
     */
    public function getProductRoundMethod()
    {
        return $this->getData(self::PRODUCT_ROUND_METHOD);
    }

    /**
     * Get product_round_value
     *
     * return string
     */
    public function getProductRoundValue()
    {
        return $this->getData(self::PRODUCT_ROUND_VALUE);
    }

    /**
     * Get category_position
     *
     * return string
     */
    public function getCategoryPosition()
    {
        return $this->getData(self::CATEGORY_POSITION);
    }

    /**
     * Get category_image
     *
     * return string
     */
    public function getCategoryImage()
    {
        return $this->getData(self::CATEGORY_IMAGE);
    }

    /**
     * Get category_image_width
     *
     * return string
     */
    public function getCategoryImageWidth()
    {
        return $this->getData(self::CATEGORY_IMAGE_WIDTH);
    }

    /**
     * Get category_image_height
     *
     * return string
     */
    public function getCategoryImageHeight()
    {
        return $this->getData(self::CATEGORY_IMAGE_HEIGHT);
    }

    /**
     * Get category_custom_style
     *
     * return string
     */
    public function getCategoryCustomStyle()
    {
        return $this->getData(self::CATEGORY_CUSTOM_STYLE);
    }

    /**
     * Get category_text
     *
     * return string
     */
    public function getCategoryText()
    {
        return $this->getData(self::CATEGORY_TEXT);
    }

    /**
     * Get category_custom_url
     *
     * return string
     */
    public function getCategoryCustomUrl()
    {
        return $this->getData(self::CATEGORY_CUSTOM_URL);
    }

    /**
     * Get category_round_method
     *
     * return string
     */
    public function getCategoryRoundMethod()
    {
        return $this->getData(self::CATEGORY_ROUND_METHOD);
    }

    /**
     * Get category_round_value
     *
     * return string
     */
    public function getCategoryRoundValue()
    {
        return $this->getData(self::CATEGORY_ROUND_VALUE);
    }

    /**
     * Set label_id
     *
     * @param int $labelId
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setLabelId($labelId)
    {
        return $this->setData(self::LABEL_ID, $labelId);
    }

    /**
     * Set title
     *
     * @param string $title
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set stores
     *
     * @param string $storeId
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Set status
     *
     * @param int $status
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set Customer Groups
     *
     * @param int $customerGroups
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setCustomerGroups($customerGroups)
    {
        return $this->setData(self::CUSTOMER_GROUPS, $customerGroups);
    }

    /**
     * Set conditions_serialized
     *
     * @param string $conditionsSerialized
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * Set product_position
     *
     * @param string $productPosition
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setProductPosition($productPosition)
    {
        return $this->setData(self::PRODUCT_POSITION, $productPosition);
    }

    /**
     * Set product_image
     *
     * @param string $productImage
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setProductImage($productImage)
    {
        return $this->setData(self::PRODUCT_IMAGE, $productImage);
    }

    /**
     * Set product_image_width
     *
     * @param string $productImageWidth
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setProductImageWidth($productImageWidth)
    {
        return $this->setData(self::PRODUCT_IMAGE_WIDTH, $productImageWidth);
    }

    /**
     * Set product_image_height
     *
     * @param string $productImageHeight
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setProductImageHeight($productImageHeight)
    {
        return $this->setData(self::PRODUCT_IMAGE_HEIGHT, $productImageHeight);
    }

    /**
     * Set product_custom_style
     *
     * @param string $productCustomStyle
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setProductCustomStyle($productCustomStyle)
    {
        return $this->setData(self::PRODUCT_CUSTOM_STYLE, $productCustomStyle);
    }

    /**
     * Set product_text
     *
     * @param string $productText
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setProductText($productText)
    {
        return $this->setData(self::PRODUCT_TEXT, $productText);
    }

    /**
     * Set product_custom_url
     *
     * @param string $productCustomUrl
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setProductCustomUrl($productCustomUrl)
    {
        return $this->setData(self::PRODUCT_CUSTOM_URL, $productCustomUrl);
    }

    /**
     * Set product_round_method
     *
     * @param string $productRoundMethod
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setProductRoundMethod($productRoundMethod)
    {
        return $this->setData(self::PRODUCT_ROUND_METHOD, $productRoundMethod);
    }

    /**
     * Set product_round_value
     *
     * @param string $productRoundValue
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setProductRoundValue($productRoundValue)
    {
        return $this->setData(self::PRODUCT_ROUND_VALUE, $productRoundValue);
    }

    /**
     * Set category_position
     *
     * @param string $categoryPosition
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setCategoryPosition($categoryPosition)
    {
        return $this->setData(self::CATEGORY_POSITION, $categoryPosition);
    }

    /**
     * Set category_image
     *
     * @param string $categoryImage
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setCategoryImage($categoryImage)
    {
        return $this->setData(self::CATEGORY_IMAGE, $categoryImage);
    }

    /**
     * Set category_image_width
     *
     * @param string $categoryImageWidth
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setCategoryImageWidth($categoryImageWidth)
    {
        return $this->setData(self::CATEGORY_IMAGE_WIDTH, $categoryImageWidth);
    }

    /**
     * Set category_image_height
     *
     * @param string $categoryImageHeight
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setCategoryImageHeight($categoryImageHeight)
    {
        return $this->setData(self::CATEGORY_IMAGE_HEIGHT, $categoryImageHeight);
    }

    /**
     * Set category_custom_style
     *
     * @param string $categoryCustomStyle
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setCategoryCustomStyle($categoryCustomStyle)
    {
        return $this->setData(self::CATEGORY_CUSTOM_STYLE, $categoryCustomStyle);
    }

    /**
     * Set category_text
     *
     * @param string $categoryText
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setCategoryText($categoryText)
    {
        return $this->setData(self::CATEGORY_TEXT, $categoryText);
    }

    /**
     * Set category_custom_url
     *
     * @param string $categoryCustomUrl
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setCategoryCustomUrl($categoryCustomUrl)
    {
        return $this->setData(self::CATEGORY_CUSTOM_URL, $categoryCustomUrl);
    }

    /**
     * Set category_round_method
     *
     * @param string $categoryRoundMethod
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setCategoryRoundMethod($categoryRoundMethod)
    {
        return $this->setData(self::CATEGORY_ROUND_METHOD, $categoryRoundMethod);
    }

    /**
     * Set category_round_value
     *
     * @param string $categoryRoundValue
     * return \Swissup\Prolabels\Api\Data\LabelInterface
     */
    public function setCategoryRoundValue($categoryRoundValue)
    {
        return $this->setData(self::CATEGORY_ROUND_VALUE, $categoryRoundValue);
    }
}
