<?php
namespace Swissup\Attributepages\Block;

class ProductList extends \Magento\Catalog\Block\Product\ListProduct
    implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;
    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;
    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditionsHelper;
    /**
     * Page view helper
     *
     * @var \Swissup\Attributepages\Helper\Page\View
     */
    protected $pageViewHelper;
    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param \Swissup\Attributepages\Helper\Page\View $pageViewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Swissup\Attributepages\Helper\Page\View $pageViewHelper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->sqlBuilder = $sqlBuilder;
        $this->rule = $rule;
        $this->conditionsHelper = $conditionsHelper;
        $this->pageViewHelper = $pageViewHelper;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }
    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->productCollectionFactory->create();
            $this->_catalogLayer->prepareProductCollection($collection);
            $collection->addStoreFilter();
            $this->prepareProductCollection($collection);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    public function getProductCollection()
    {
        return $this->_getProductCollection();
    }
    /**
     * Use this method to apply manual filters, etc
     *
     * @param  \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return void
     */
    public function prepareProductCollection($collection)
    {
        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        try {
            $currentPage = $this->pageViewHelper
                ->getRegistryObject('attributepages_current_page');
            $attributeCode = $currentPage->getAttribute()->getAttributeCode();
            $optionId = $currentPage->getOptionId();
            $collection->addAttributeToFilter($attributeCode, ['finset' => $optionId]);
        } catch (\Exception $e) {
            $this->setTemplate(null);
            $this->setCustomTemplate(null);
        }
    }
    /**
     * @return \Magento\Rule\Model\Condition\Combine
     */
    protected function getConditions()
    {
        $conditions = $this->getData('conditions_encoded')
            ? $this->getData('conditions_encoded')
            : $this->getData('conditions');
        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }
        $this->rule->loadPost(['conditions' => $conditions]);
        return $this->rule->getConditions();
    }
}
