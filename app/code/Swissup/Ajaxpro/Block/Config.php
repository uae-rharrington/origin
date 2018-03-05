<?php
/**
 * Copyright © 2016 Swissup. All rights reserved.
 */
namespace Swissup\Ajaxpro\Block;

use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Catalog\Block\Product\AbstractProduct
{
    const VALIDATION = 'ajaxpro/main/validation';

    /**
     *
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        $this->productMetadata = $productMetadata;
        parent::__construct($context, $data);
    }
    /**
     *
     * @return bool
     */
    public function isForceValidation()
    {
        return (bool) $this->_scopeConfig->getValue(
            self::VALIDATION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     *
     * @return boolean
     */
    public function isNeedBindSubmitFix()
    {
        $version = $this->productMetadata->getVersion();
        list($version) = explode('-', $version);

        return version_compare($version, '2.0.0', '>')
            && version_compare($version, '2.2.0', '<');
    }
}
