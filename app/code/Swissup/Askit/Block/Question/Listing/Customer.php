<?php

namespace Swissup\Askit\Block\Question\Listing;

use Swissup\Askit\Block\Question\Listing;

class Customer extends Listing
{
    /**
     *
     * @return \Swissup\Askit\Model\ResourceModel\Question\Collection
     */
    public function getCollection()
    {
        if (empty($this->_collection)) {
            $collection = parent::getCollection();

            $customerId = (int) $this->_customerSession->getId();
            $collection->addCustomerFilter($customerId);

            $this->_collection = $collection;
        }

        return $this->_collection;
    }
}
