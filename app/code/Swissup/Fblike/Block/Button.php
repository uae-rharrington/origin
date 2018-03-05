<?php

namespace Swissup\Fblike\Block;

class Button extends \Magento\Framework\View\Element\Template
{

    private $configSection = 'category';

    public function getConfigSection()
    {
        return $this->configSection;
    }

    public function setConfigSection($value)
    {
        $this->configSection = $value;
        return $this;
    }

    public function getLikeButtonConfig()
    {
        return $this->_scopeConfig->getValue(
            "fblike/" . $this->getConfigSection(),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getProductUrl()
    {
        $product = $this->getProduct();
        if (isset($product)) {
            $oldRequestPath = $product->getData('request_path');
            $product->setData('request_path', '');
            $params = ['_ignore_category' => true];
            $url = $product->getUrlModel()->getUrl($product, $params);
            $product->setData('request_path', $oldRequestPath);
            return $url;
        }
        return '';
    }
}
