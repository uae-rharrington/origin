<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Controller\Adminhtml\QuoteRequest;

class Index extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Quote Request grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Quotes'));
        return $resultPage;
    }
}
