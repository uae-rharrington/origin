<?php
/**
 * Copyright © 2016 Swissup. All rights reserved.
 */
namespace Swissup\Ajaxsearch\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Init extends \Magento\Framework\View\Element\Template
{
    // const PLACEHOLDER = 'ajaxsearch/main/placeholder';
    const LIMIT       = 'ajaxsearch/main/limit';
    const HIGHLIGHT   = 'ajaxsearch/main/highlight';
    const HINT        = 'ajaxsearch/main/hint';
    const CLASSNAMES  = 'ajaxsearch/main/classNames';


    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $localeFormat;

    /**
     * @var \Magento\Framework\Module\PackageInfo
     */
    protected $packageInfo;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\Module\PackageInfo $packageInfo
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Module\PackageInfo $packageInfo,
        array $data = []
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->localeFormat = $localeFormat;
        $this->packageInfo = $packageInfo;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function getJsonConfig()
    {
        $package = 'swissup/ajaxsearch';
        $module = $this->packageInfo->getModuleName($package);
        $config = [
            'priceFormat' => $this->localeFormat->getPriceFormat(),
            'package' => $package,
            'module' => $module,
            'version' => $this->packageInfo->getVersion($module)
        ];
        return $this->jsonEncoder->encode($config);
    }

    /**
     *
     * @param  string $key
     * @return string
     */
    protected function getScopeConfig($key)
    {
        return $this->_scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     *
     * @return int
     */
    public function getLimit()
    {
        return (int) $this->getScopeConfig(self::LIMIT);
    }

    // /**
    //  *
    //  * @return string
    //  */
    // public function getPlaceholder()
    // {
    //     return $this->getScopeConfig(self::PLACEHOLDER);
    // }
    /**
     *
     * @return boolean
     */
    public function isHighligth()
    {
        return (bool) $this->getScopeConfig(self::HIGHLIGHT);
    }

    /**
     *
     * @return boolean
     */
    public function isHint()
    {
        return (bool) $this->getScopeConfig(self::HINT);
    }

    /**
     *
     * @return string [json]
     */
    public function getClassNames()
    {
        return $this->jsonEncoder->encode($this->getScopeConfig(self::CLASSNAMES));
    }
}
