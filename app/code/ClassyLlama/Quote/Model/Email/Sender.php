<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Model\Email;

class Sender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    /**
     * @inheritdoc
     */
    protected function prepareTemplate(\Magento\Sales\Model\Order $order)
    {
        parent::prepareTemplate($order);

        if ($order->getIsQuoteRequest()) {
            if ($order->getCustomerIsGuest()) {
                $templateId = $this->identityContainer->getQuoteGuestTemplateId();
            } else {
                $templateId = $this->identityContainer->getQuoteTemplateId();
            }
            $this->templateContainer->setTemplateId($templateId);
        }
    }
}
