<?php

namespace Swissup\Ajaxpro\CustomerData;

use Magento\Framework;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Swissup\Ajaxpro\CustomerData\AbstractCustomerData;

class Cart extends AbstractCustomerData implements SectionSourceInterface
{
    const ENABLE = 'ajaxpro/main/cart';
    const CART_HANDLE = 'ajaxpro/main/cartHandle';

    /**
     * @var Quote|null
     */
    protected $quote = null;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\View\Layout\View\Layout\BuilderFactory $layoutBuilderFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\View\Page\Layout\Reader $pageLayoutReader
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Layout\BuilderFactory $layoutBuilderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Page\Layout\Reader $pageLayoutReader,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct($layoutFactory, $layoutBuilderFactory, $context, $pageLayoutReader, $data);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $enable = (bool) $this->getConfig(self::ENABLE);
        if (!$enable) {
            return [];
        }
        if (!$this->getQuote() || !$this->getQuote()->getId()) {
            return [];
        }
        $checkoutCartHandle = $this->getConfig(self::CART_HANDLE);
        $return  = [
            // 'params' => $ajaxpro,
            // 'test' => md5(time()),
            'checkout.cart' => $this->getBlockHtml(
                'checkout.cart',
                [$checkoutCartHandle]
            ),
            'checkout.cart.after' => $this->getBlockHtml(
                'ajaxpro.checkout.cart.after',
                ['ajaxpro_popup_checkout_cart_index']
            ),
            'reinit' => $this->getBlockHtml('ajaxpro.init', ['default'])
        ];

        // foreach ($return as $key => &$block) {
        //     $block .= '<script type="text/javascript">console.log("'
        //         . $key . ' ' . md5($block)
        //         . '");</script>';
        // }
        $this->flushLayouts();

        return $return;
    }

    /**
     * Get active quote
     *
     * @return Quote
     */
    public function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }
}
