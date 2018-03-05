<?php
/**
 * Copyright © 2015 Swissup. All rights reserved.
 */
namespace Swissup\Fblike\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Like extends AbstractHelper
{

    /**
     * Layout
     *
     * @var \Magento\Framework\View\Layout
     */
    private $layout;

    /**
     * [$renderer description]
     *
     * @var \Swissup\Fblike\Block\Button
     */
    private $renderer;

    public function __construct(
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->layout = $layout;
        parent::__construct($context);
    }

    /**
     * [getProductLike description]
     * @param  \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductLike(\Magento\Catalog\Model\Product $product)
    {
        return $this->renderButton($product, 'category');
    }

    public function renderButton(
        \Magento\Catalog\Model\Product $product,
        $section
    ) {
        return $this->_getRenderer()
            ->setProduct($product)
            ->setConfigSection($section)
            ->toHtml();
    }

    private function _getRenderer()
    {
        if (!isset($this->renderer)) {
            $this->renderer = $this->layout
                ->createBlock('\Swissup\Fblike\Block\Button')
                ->setTemplate('button.phtml');
        }
        return $this->renderer;
    }
}
