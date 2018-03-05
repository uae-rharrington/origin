<?php
namespace Swissup\ProLabels\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Context;
use Magento\Store\Model\ScopeInterface;

class Labels extends Template
{
    /**
     * @var \Swissup\ProLabels\Helper\ProductLabels
     */
    protected $_systemLabels;
    /**
     * @var \Swissup\ProLabels\Model\Label
     */
    protected $_labelModel;
    /**
     * @var array
     */
    protected $_labels = [];
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Swissup\ProLabels\Helper\ProductLabels $systemLabels,
     * @param \Swissup\ProLabels\Model\Label $labelModel,
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Swissup\ProLabels\Helper\ProductLabels $systemLabels,
        \Swissup\ProLabels\Model\Label $labelModel,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->_systemLabels = $systemLabels;
        $this->_coreRegistry = $registry;
        $this->_labelModel = $labelModel;
        $this->_httpContext = $httpContext;
        parent::__construct($context, $data);
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
        $product = $this->_coreRegistry->registry('product');
        $labelIds = $this->_labelModel->getProductLabels($product->getId());
        return [
            'PROLABELS_LABELS',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->_httpContext->getValue(Context::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            implode(",", $labelIds),
            $product->getId()
        ];
    }

    protected function _initDefaultLabels(\Magento\Catalog\Model\Product $product)
    {
        if ($onSale = $this->_systemLabels->getOnSaleLabel($product, "product")) {
            $this->_labels[$onSale->getPosition()][] = $onSale;
        }
        if ($isNew = $this->_systemLabels->getIsNewLabel($product, "product")) {
            $this->_labels[$isNew->getPosition()][] = $isNew;
        }
        if ($inStock = $this->_systemLabels->getStockLabel($product, "product")) {
            $this->_labels[$inStock->getPosition()][] = $inStock;
        }
        if ($outOfStock = $this->_systemLabels->getOutOfStockLabel($product, "product")) {
            $this->_labels[$outOfStock->getPosition()][] = $outOfStock;
        }
    }

    protected function _initManualLabels(\Magento\Catalog\Model\Product $product)
    {
        $labelIds = $this->_labelModel->getProductLabels($product->getId());
        if (count($labelIds) == 0) {
            return false;
        }
        $collection = $this->_labelModel->getCollection();
        $collection->addFieldToFilter('label_id', $labelIds);
        $collection->addFieldToFilter('status', 1);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $customerGroupId = $customerSession->getCustomerGroupId();
        $storeId = $this->_storeManager->getStore()->getId();
        foreach ($collection as $label) {
            $labelStores = $label['store_id'];
            if (!in_array('0', $labelStores)) {
                if (!in_array($storeId, $labelStores)) { continue; }
            }
            $labelGroupIds = unserialize($label->getCustomerGroups());
            if (!in_array($customerGroupId, $labelGroupIds)) {
                continue;
            }
            $labelConfig = [
                'position' => $label->getProductPosition(),
                'text' => $label->getProductText(),
                'custom' => $label->getProductCustomStyle(),
                'custom_url' => $label->getProductCustomUrl(),
                'round_method' => $label->getProductRoundMethod(),
                'round_value' => $label->getProductRoundValue(),
                'image' => $label->getProductImage()
            ];
            $labelData = $this->_systemLabels->getLabelOutputObject($labelConfig, $product, "product");
            $this->_labels[$labelData->getPosition()][] = $labelData;
        }
    }

    public function getLabels()
    {
        return $this->_labels;
    }

    public function getLabelImage($configImage)
    {
        return $this->_systemLabels->getUploadedLabelImage($configImage, "product");
    }

    public function getLabelImagePath($configImage)
    {
        return $this->_systemLabels->getUploadedLabelImagePath($configImage, "product");
    }

    public function getBaseImageWrapConfig()
    {
        return $this->_scopeConfig->getValue("prolabels/general/base", ScopeInterface::SCOPE_STORE);
    }

    public function getContentWrapConfig()
    {
        return $this->_scopeConfig->getValue("prolabels/general/content", ScopeInterface::SCOPE_STORE);
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $product = $this->_coreRegistry->registry('product');
        $this->_initDefaultLabels($product);
        $this->_initManualLabels($product);
        return parent::_beforeToHtml();
    }
}
