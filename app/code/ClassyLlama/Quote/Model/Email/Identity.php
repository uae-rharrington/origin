<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Model\Email;

class Identity extends \Magento\Sales\Model\Order\Email\Container\OrderIdentity
{
    /**
     * Path to Quote Request Guest Email Template
     */
    const XML_PATH_QUOTE_GUEST_TEMPLATE = 'quoterequest/email/guest_template';

    /**
     * Path to Quote Request Customer Email Template
     */
    const XML_PATH_QUOTE_TEMPLATE = 'quoterequest/email/customer_template';

    /**
     * Return Quote Request Guest Template Id
     *
     * @return mixed
     */
    public function getQuoteGuestTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_QUOTE_GUEST_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * Return Quote Request Customer Template Id
     *
     * @return mixed
     */
    public function getQuoteTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_QUOTE_TEMPLATE, $this->getStore()->getStoreId());
    }
}
