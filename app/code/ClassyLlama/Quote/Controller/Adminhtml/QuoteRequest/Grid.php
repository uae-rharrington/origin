<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Controller\Adminhtml\QuoteRequest;

class Grid extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Order grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
