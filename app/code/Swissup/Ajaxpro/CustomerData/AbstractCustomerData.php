<?php

namespace Swissup\Ajaxpro\CustomerData;

use Magento\Framework;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

abstract class AbstractCustomerData extends \Magento\Framework\DataObject implements SectionSourceInterface
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var Magento\Framework\View\Layout\BuilderFactory
     */
    protected $layoutBuilderFactory;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\Page\Layout\Reader
     */
    protected $pageLayoutReader;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var array
     */
    protected $layouts = [];

    /**
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\View\Layout\View\Layout\BuilderFactory $layoutBuilderFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\View\Page\Layout\Reader $pageLayoutReader
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Layout\BuilderFactory $layoutBuilderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Page\Layout\Reader $pageLayoutReader,
        array $data = []
    ) {
        parent::__construct($data);
        $this->layoutFactory = $layoutFactory;
        $this->layoutBuilderFactory = $layoutBuilderFactory;
        $this->pageConfig = $context->getPageConfig();
        $this->pageLayoutReader = $pageLayoutReader;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $return  = [];

        // foreach ($return as $key => &$block) {
        //     $block .= '<script type="text/javascript">console.log("'
        //         . $key . ' ' . md5($block)
        //         . '");</script>';
        // }
        $this->flushLayouts();

        return $return;
    }

    /**
     *
     * @param  string $key
     * @return string
     */
    protected function getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve subtotal block html
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getBlockHtml($blockName, $handles)
    {
        $layout = $this->getCustomLayoutByHandles($handles);

        $block = $layout->getBlock($blockName);

        if ($block) {
            $html = $block->toHtml();
        } else {
            $html = $layout->renderNonCachedElement($blockName);
        }

        $html = (!empty($html) ? $html : ' block not exist ' . time()
            . "\n xml <!-- " . $layout->getUpdate()->asString() . " -->"
            . "\n output <!--  " . $layout->getOutput() . " -->"
            . "\n handles <!--  " . implode(", ", $layout->getUpdate()->getHandles()) . " -->"
            )
        ;
        // $layout->__destruct();

        return $html;
    }

    protected function getCustomLayoutByHandles($handles)
    {
        $key = implode(':', $handles);
        if (!isset($this->layouts[$key])) {
            $fullActionName = end($handles);

            $layout = $this->layoutFactory->create();

            $builder = $this->layoutBuilderFactory->create(\Magento\Framework\View\Layout\BuilderFactory::TYPE_PAGE, [
                'layout' => $layout,
                'pageConfig' => $this->pageConfig,
                'pageLayoutReader' => $this->pageLayoutReader
            ]);
            $builder->setCustomHandles($handles)
                ->setFullActionName($fullActionName);
            $layout->setBuilder($builder);

            $this->layouts[$key] = $layout;
        }

        return $this->layouts[$key];
    }

    protected function flushLayouts()
    {
        foreach ($this->layouts as $layout) {
            $layout->__destruct();
        }
        return $this;
    }

    /**
     *
     * @param array|null $parameters page parameters
     * @param string|null $defaultHandle
     * @return bool
     */
    public function generatePageLayoutHandles(array $parameters = [], $defaultHandle = null)
    {
        $handle = $defaultHandle ? $defaultHandle : $this->getDefaultLayoutHandle();
        $pageHandles = [$handle];
        foreach ($parameters as $key => $value) {
            $pageHandles[] = $handle . '_' . $key . '_' . $value;
        }
        return $pageHandles;
    }
}
