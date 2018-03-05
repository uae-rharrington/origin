<?php

namespace Swissup\Highlight\Block\ProductList;

class All extends \Magento\Catalog\Block\Product\ListProduct implements \Magento\Widget\Block\BlockInterface
{
    const PAGE_TYPE = null;

    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Swissup\Highlight\Block\ProductList\Toolbar';

    /**
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $widgetPager;

    protected $widgetPageVarName = 'hap';

    /**
     * @var \Swissup\Highlight\Block\ProductList\Toolbar
     */
    protected $toolbar;

    protected $widgetPriceSuffix = 'all';

    protected $widgetCssClass = 'highlight-all';

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Swissup\Highlight\Helper\Page
     */
    protected $pageHelper;

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
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Swissup\Highlight\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\App\Http\Context $httpContext,
     * @param \Swissup\Highlight\Helper\Page $highlightHelper,
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Swissup\Highlight\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Swissup\Highlight\Helper\Page $highlightHelper,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->httpContext = $httpContext;
        $this->pageHelper = $highlightHelper;
        $this->sqlBuilder = $sqlBuilder;
        $this->rule = $rule;
        $this->conditionsHelper = $conditionsHelper;
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
            $collection = $this->productCollectionFactory->create($this->getProductCollectionType());
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

    public function getProductCollectionType()
    {
        return \Swissup\Highlight\Model\ResourceModel\Product\CollectionFactory::TYPE_DEFAULT;
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

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }


    /**********************************************************
    ******************** Widget Specific Methods **************
    **********************************************************/
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
        if (false === $this->getIsWidget()) {
            return parent::getCacheKeyInfo();
        }

        return [
            'HIGHLIGHT',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            $this->getAttributeCode(),
            $this->getMode(),
            $this->getOrder(),
            $this->getDir(),
            $this->getCurrentPage(),
            $this->getProductsCount(),
            $this->getProductsPerPage(),
            $this->showPager(),
            $this->getPriceSuffix(),
            $this->getCssClass(),
            $this->getPageVarName()
        ];
    }

    public function getTemplate()
    {
        if (empty($this->_template)) {
            $this->_template = $this->getCustomTemplate();
        }
        return $this->_template;
    }

    public function getToolbarBlock()
    {
        if ($this->toolbar) {
            return $this->toolbar;
        }

        $toolbar = parent::getToolbarBlock();
        $this->initToolbar($toolbar);
        $this->toolbar = $toolbar;

        return $toolbar;
    }

    /**
     * Use this method to apply manual sort order, etc
     *
     * @param  \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
     * @return void
     */
    protected function initToolbar($toolbar)
    {
        $orders = array_keys($toolbar->getAvailableOrders());
        $defaultOrder = $this->getDefaultSortField();
        if (!in_array($defaultOrder, $orders)) {
            $toolbar->addOrderToAvailableOrders($this->getDefaultSortField(), $this->getDefaultSortFieldLabel());
            $toolbar->setDefaultOrder($this->getDefaultSortField());
            $toolbar->setDefaultDirection($this->getDefaultSortDirection());
        }

        if (false !== $this->getIsWidget()) {
            // $toolbar->setData('_current_grid_mode', $this->getMode());
            $toolbar->setData('_current_limit', $this->getPageSize());
            $toolbar->setData('_current_page', $this->getCurrentPage());
            $toolbar->setData('_current_grid_direction', $this->getDir());

            // additional sort order parameter, use it to sort by attribute
            if ($this->hasOrder() && $this->getOrder() !== 'default') {
                $order = $this->getOrder();
                $toolbar->setSkipOrder(true);
                if (in_array(strtolower($order), ['rand()', 'rand', 'random'])) {
                    $this->getProductCollection()->getSelect()->order(new \Zend_Db_Expr('RAND()'));
                } else {
                    $this->getProductCollection()->setOrder($order, $this->getDir());
                }
            }
        }

        // sort by column, alias, etc
        if ($this->getRawOrder()) {
            $toolbar->setSkipOrder(true);
            $this->getProductCollection()->getSelect()->order($this->getRawOrder());
        }
    }

    public function getDefaultSortField()
    {
        return 'position';
    }

    public function getDefaultSortDirection()
    {
        return 'ASC';
    }

    public function getDir()
    {
        if (!$this->hasData('dir')) {
            return $this->getDefaultSortDirection();
        }
        return $this->getData('dir');
    }

    /**
     * Get number of current page based on query value
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return abs((int)$this->getRequest()->getParam($this->getPageVarName()));
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            return 10;
        }
        return $this->getData('products_count');
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsPerPage()
    {
        if (!$this->hasData('products_per_page')) {
            return 5;
        }
        return $this->getData('products_per_page');
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            return false;
        }
        return (bool)$this->getData('show_pager');
    }

    /**
     * Retrieve how many products should be displayed on page
     *
     * @return int
     */
    protected function getPageSize()
    {
        return $this->showPager() ? $this->getProductsPerPage() : $this->getProductsCount();
    }

    public function getPriceSuffix()
    {
        if (!$this->hasData('price_suffix')) {
            $this->setData('price_suffix', $this->widgetPriceSuffix);
        }
        return $this->getData('price_suffix');
    }

    public function getCssClass()
    {
        return $this->widgetCssClass . (
            $this->hasData('css_class') ? ' ' . $this->getData('css_class') : ''
        );
    }

    public function getPageVarName()
    {
        if (!$this->hasData('page_var_name')) {
            $this->setData('page_var_name', $this->widgetPageVarName);
        }
        return $this->getData('page_var_name');
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        if (false === $this->getIsWidget()) {
            return parent::getMode();
        }
        return $this->getData('mode');
    }

    public function getPageUrl()
    {
        if ($this->hasData('page_url')) {
            return $this->pageHelper->getDirectUrl($this->getData('page_url'));
        }

        if (!static::PAGE_TYPE) {
            return false;
        }
        return $this->pageHelper->getPageUrl(static::PAGE_TYPE);
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        if ($this->showPager()) {
            if (!$this->widgetPager) {
                $this->widgetPager = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Product\Widget\Html\Pager',
                    $this->getToolbarBlock()->getNameInLayout() . '_pager'
                );

                $this->widgetPager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName($this->getPageVarName())
                    ->setLimit($this->getPageSize())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }
            if ($this->widgetPager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->widgetPager->toHtml();
            }
        }
        return '';
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

            /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }
}
