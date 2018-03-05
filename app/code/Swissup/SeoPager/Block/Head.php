<?php

namespace Swissup\SeoPager\Block;

use Swissup\SeoPager\Model\Config\Source\Strategy;

class Head extends \Magento\Framework\View\Element\Template
{

    /**
     * @var Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    protected $toolbar;

    public function __construct(
        \Swissup\SeoPager\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->helper = $helper;
        return parent::__construct($context, $data);
    }

    /**
     * Get listing pager block
     *
     * @return null|Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbar()
    {
        if (!isset($this->toolbar)) {
            $productList = $this->getLayout()->getBlock('category.products.list');
            if ($productList) {
                $collection = $productList->getLoadedProductCollection(); // initialize toolbar block
                $toolbar = $productList->getToolbarBlock();
                if (!$toolbar->getCollection()) {
                    $toolbar->setCollection($collection);
                }

                $this->toolbar = $toolbar;
            }
        }

        return $this->toolbar;
    }

    /**
     * Before rendering html (check if toolbar found)
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        if (($this->helper->getPresentationStrategy() == Strategy::LEAVE_AS_IS)
            || !$this->getToolbar()
        ) {
            $this->setTemplate('');
        }

        return parent::_beforeToHtml();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($this->canUseCanonical() || $this->canUsePrevNext())
        {
            // remove Magento generated rel="canonical"
            foreach ($this->pageConfig->getAssetCollection()->getAll() as $identifier => $asset) {
                if ($asset->getContentType() == 'canonical') {
                    $this->pageConfig->getAssetCollection()->remove($identifier);
                }
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Check if rel="canonical" is allowed
     *
     * @return bool
     */
    public function canUseCanonical()
    {
        return $this->helper->getPresentationStrategy() == Strategy::REL_CANONICAL;
    }

    /**
     * Check if rel="prev" and rel="next" is allowed
     *
     * @return bool
     */
    public function canUsePrevNext()
    {
        return $this->helper->getPresentationStrategy() == Strategy::REL_NEXT_REL_PREV;
    }

    /**
     * Get next page url
     *
     * @return string
     */
    public function getNextPageUrl()
    {
        $pager = $this->getLayout()->getBlock('product_list_toolbar_pager');
        if ($pager) {
            return $pager->getPageUrl($this->getToolbar()->getCurrentPage()+1);
        }

        return '';
    }

    /**
     * Get previous page url
     *
     * @return string
     */
    public function getPreviousPageUrl()
    {
        $pager = $this->getLayout()->getBlock('product_list_toolbar_pager');
        if ($pager) {
            return $this->getToolbar()->getCurrentPage() > 2
                ? $pager->getPageUrl($this->getToolbar()->getCurrentPage()-1)
                : $pager->getPageUrl(null);
        }

        return '';
    }

    /**
     * Check if current page is a fisrt page
     *
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getToolbar()->isFirstPage();
    }

    /**
     * Check if current page is a last page
     *
     * @return bool
     */
    public function isLastPage()
    {
        return $this->getToolbar()->getCurrentPage() >= $this->getToolbar()->getLastPageNum();
    }

    public function getViewAllPageUrl()
    {
        return $this->helper->getViewAllPageUrl(false);
    }

}
