<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Model\ResourceModel\Customer\Orders;

class Collection extends \Magento\Reports\Model\ResourceModel\Customer\Orders\Collection
{
    /**
     * Before load action
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->addFieldToFilter('is_quote_request', ['eq' => '0']);
        parent::_beforeLoad();

        return $this;
    }
}
