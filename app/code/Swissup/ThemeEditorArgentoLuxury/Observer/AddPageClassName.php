<?php
namespace Swissup\ThemeEditorArgentoLuxury\Observer;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\ObserverInterface;

class AddPageClassName implements ObserverInterface
{
    /**
     * Path to store config is fullscreen slider enabled
     */
    const FULLSCREEN_SLIDER = 'swissup_argento_luxury/homepage/fullscreen_slider';

     /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->pageConfig = $pageConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $action = $observer->getEvent()->getFullActionName();
        $fullscreenSliderEnabled = $this->scopeConfig->getValue(
            self::FULLSCREEN_SLIDER,
            ScopeInterface::SCOPE_STORE
        );

        if ($fullscreenSliderEnabled && $action == 'cms_index_index') {
            $this->pageConfig->addBodyClass('luxury-fullscreen-slider');
        }
    }
}
