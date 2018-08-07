<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Block\Adminhtml\QuoteRequest;

class View extends \Magento\Sales\Block\Adminhtml\Order\View
{
    /**
     * @var \Magento\Framework\Url
     */
    private $frontendUrlBuilder;

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param \Magento\Framework\Url $frontendUrlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Magento\Framework\Url $frontendUrlBuilder,
        array $data
    ) {
        $this->frontendUrlBuilder = $frontendUrlBuilder;

        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _construct()
    {
        $order = $this->getOrder();

        if (!$order) {
            return;
        }

        $this->store = $order->getStore();

        parent::_construct();

        $this->buttonList->remove('order_cancel');
        $this->buttonList->remove('order_hold');
        $this->buttonList->remove('order_invoice');
        $this->buttonList->remove('order_ship');
        $this->buttonList->remove('send_notification');
        $this->buttonList->remove('order_reorder');
        $this->buttonList->remove('order_edit');

        $this->setId('sales_quoterequest_view');

        if ($this->_isAllowedAction('Magento_Sales::emails') && !$order->isCanceled()) {
            $message = __('Are you sure you want to send a quote request email to customer?');
            $this->addButton(
                'send_notification',
                [
                    'label' => __('Send Email'),
                    'class' => 'send-email',
                    'onclick' => "confirmSetLocation('{$message}', '{$this->getEmailUrl()}')"
                ]
            );
        }

        if ($this->_isAllowedAction('Magento_Sales::reorder')
            && $this->_reorderHelper->isAllowed($order->getStore())
            && $order->canReorderIgnoreSalable()
        ) {
            $this->buttonList->add(
                'order_reorder',
                [
                    'label' => __('Order'),
                    'onclick' => "window.open('{$this->getReorderUrl()}', '_blank')",
                    'class' => 'reorder'
                ]
            );
        }

        if ($this->_isAllowedAction('Magento_Sales::actions_edit') && $order->canEdit()) {
            $onclickJs = 'jQuery(\'#order_edit\').orderEditDialog({message: \''
                . $this->getEditMessage($order) . '\', url: \'' . $this->getEditUrl()
                . '\'}).orderEditDialog(\'showDialog\');';

            $this->buttonList->add(
                'order_edit',
                [
                    'label' => __('Edit'),
                    'class' => 'edit primary',
                    'onclick' => $onclickJs,
                    'data_attribute' => [
                        'mage-init' => '{"orderEditDialog":{}}',
                    ]
                ]
            );
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Framework\Phrase
     */
    protected function getEditMessage($order)
    {
        // see if order has non-editable products as items
        $nonEditableTypes = $this->getNonEditableTypes($order);
        if (!empty($nonEditableTypes)) {
            return __(
                'This quote request contains (%1) items and therefore cannot be edited through the admin interface. ' .
                'If you wish to continue editing, the (%2) items will be removed, ' .
                ' the quote will be canceled and a new quote will be requested.',
                implode(', ', $nonEditableTypes),
                implode(', ', $nonEditableTypes)
            );
        }
        return __('Are you sure? This quote request will be canceled and a new one will be created instead.');
    }

    /**
     * Reorder URL getter
     *
     * @return string
     */
    public function getReorderUrl()
    {
        if ($this->store) {
            $this->frontendUrlBuilder->setScope($this->store->getId());
        }
        
        return $this->frontendUrlBuilder->getUrl('quoterequest/cart/add', ['id' => $this->getOrder()->getId()]);
    }

    /**
     * Email URL getter
     *
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl('sales/order/email');
    }
}
