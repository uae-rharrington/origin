<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Model;

class QuoteRequestPaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'quoterequest';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
}
