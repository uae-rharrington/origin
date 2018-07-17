<?php
/**
 * Order Comment Collection
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\OrderComments\Model\ResourceModel\OrderComment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ClassyLlama\OrderComments\Model\OrderComment;
use ClassyLlama\OrderComments\Model\ResourceModel\OrderComment as ResourceOrderComment;

/**
 * ClassyLlama\OrderComments\Model\ResourceModel\OrderComment\Collection
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize Entity Model And ResourceModel
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(OrderComment::class, ResourceOrderComment::class);
    }
}