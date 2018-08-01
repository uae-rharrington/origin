<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Block\Order;

class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'ClassyLlama_Quote::order/history.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \ClassyLlama\Quote\Model\ResourceModel\QuoteRequest\FilteredCollection
     */
    private $orders;

    /**
     * @var \ClassyLlama\Quote\Model\ResourceModel\QuoteRequest\FilteredCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var \ClassyLlama\Quote\Helper\Data
     */
    private $quoteHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \ClassyLlama\Quote\Model\ResourceModel\QuoteRequest\FilteredCollectionFactory $filteredCollectionFactory
     * @param \ClassyLlama\Quote\Helper\Data $quoteHelper,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \ClassyLlama\Quote\Model\ResourceModel\QuoteRequest\FilteredCollectionFactory $filteredCollectionFactory,
        \ClassyLlama\Quote\Helper\Data $quoteHelper,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->quoteCollectionFactory = $filteredCollectionFactory;
        $this->quoteHelper = $quoteHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Quotes'));
    }

    /**
     * @return bool|\ClassyLlama\Quote\Model\ResourceModel\QuoteRequest\FilteredCollection
     */
    public function getOrders()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->quoteCollectionFactory->create($customerId)
                ->addFieldToSelect('*')
                ->setOrder('created_at', 'desc');
        }
        return $this->orders;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'quoterequest.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('quoterequest/order/view', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->quoteHelper->getAddQuoteRequestToCartUrl($order->getId());
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
