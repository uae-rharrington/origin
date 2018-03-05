<?php

namespace Swissup\Fblike\Block;

class Initialize extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve script options encoded to json
     *
     * @return string
     */
    public function getFacebookOptions()
    {
        $params = [
            'appId' => $this->_getConfigScopeStore("fblike/general/app")
        ];
        return json_encode($params);
    }

    private function _getConfigScopeStore($configPath)
    {
        return $this->_scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
