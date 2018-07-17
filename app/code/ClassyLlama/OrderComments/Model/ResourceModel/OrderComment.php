<?php
/**
 * Order Comment ResourceModel
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\OrderComments\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * ClassyLlama\OrderComments\Model\ResourceModel\OrderComment
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 */
class OrderComment extends AbstractDb
{
    /**
     * ResourceModel Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('uae_order_comment', 'entity_id');
    }
}