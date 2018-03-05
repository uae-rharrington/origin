<?php
/**
 * Copyright © 2016 Swissup. All rights reserved.
 */
namespace Swissup\Ajaxpro\Block;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Json\Encoder;

class Init extends \Magento\Framework\View\Element\Template
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var Encoder
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\Module\PackageInfo
     */
    protected $packageInfo;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Encoder $jsonEncoder
     * @param \Magento\Framework\Module\PackageInfo $packageInfo
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        Encoder $jsonEncoder,
        \Magento\Framework\Module\PackageInfo $packageInfo,
        array $data = []
    ) {

        $this->customerSession = $customerSession;
        $this->jsonEncoder = $jsonEncoder;
        $this->packageInfo = $packageInfo;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Retrieve serialized JS layout configuration ready to use in template
     *
     * @return string
     */
    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;
        $package = 'swissup/ajaxpro';
        $module = $this->packageInfo->getModuleName($package);
        $version = $this->packageInfo->getVersion($module);

        if (isset($jsLayout['components']['ajaxpro'])) {
            $jsLayout['components']['ajaxpro']['version'] = $version;
        }
        return $this->jsonEncoder->encode($jsLayout);
    }
}
