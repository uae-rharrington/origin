<?php
namespace Swissup\Suggestpage\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const SHOW_AFTER_ADDTOCART  = 'suggestpage/general/show_after_addtocart';

    protected function getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    public function isShowAfterAddToCart()
    {
        return (bool) $this->getConfig(self::SHOW_AFTER_ADDTOCART);
    }
}
