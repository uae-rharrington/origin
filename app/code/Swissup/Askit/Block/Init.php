<?php

namespace Swissup\Askit\Block;

// use Magento\Review\Model\ResourceModel\Rating\Collection as RatingCollection;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Json\Helper\Data;

class Init extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {

        $this->_jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    public function getJsConfig()
    {
        $jsConfig = $this->_jsonHelper->jsonEncode(
            $this->_scopeConfig->getValue('askit', ScopeInterface::SCOPE_STORE)
        );
        return $jsConfig;
    }
}
