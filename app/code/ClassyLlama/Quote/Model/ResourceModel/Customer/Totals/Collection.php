<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Model\ResourceModel\Customer\Totals;

class Collection extends \Magento\Reports\Model\ResourceModel\Customer\Totals\Collection
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
