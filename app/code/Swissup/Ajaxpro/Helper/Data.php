<?php

namespace Swissup\Ajaxpro\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    public function getCartPopupClassName()
    {
        $classNames = [
            'ajaxpro-modal-popup'
        ];

        $handle = $this->getConfig('ajaxpro/main/cartHandle');
        $classNames[] = str_replace(['_', ' '], '-', $handle);

        return implode(' ', $classNames);
    }
}
