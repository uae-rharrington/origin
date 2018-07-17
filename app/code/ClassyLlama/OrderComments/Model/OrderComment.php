<?php
/**
 * Order Comment Model
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\OrderComments\Model;

use Magento\Framework\Model\AbstractModel;
use ClassyLlama\OrderComments\Model\ResourceModel\OrderComment as ResourceOrderComment;
use ClassyLlama\OrderComments\Api\Data\OrderCommentInterface;

/**
 * ClassyLlama\OrderComments\Model\OrderComment
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 */
class OrderComment extends AbstractModel implements OrderCommentInterface
{
    /**#@+
     * Order Comment Type
     */
    const COMMENT_TYPE_ORDER = 'order';
    const COMMENT_TYPE_QUOTE = 'quote';
    /**#@-*/

    /**
     * Model Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceOrderComment::class);
    }

    /**
     * Retrieve Order Comment Id
     *
     * @api
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set Order Comment Id
     *
     * @api
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Retrieve Order Id
     *
     * @api
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Set Order Id
     *
     * @api
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Retrieve Order Comment Type
     *
     * @api
     * @return string|null
     */
    public function getOrderType()
    {
        return $this->getData(self::ORDER_TYPE);
    }

    /**
     * Set Order Comment Type
     *
     * @api
     * @param string $type
     * @return $this
     */
    public function setOrderType($type)
    {
        return $this->setData(self::ORDER_TYPE, $type);
    }

    /**
     * Retrieve Order Comment
     *
     * @api
     * @return string|null
     */
    public function getOrderComment()
    {
        return $this->getData(self::ORDER_COMMENT);
    }

    /**
     * Set Order Commnet
     *
     * @api
     * @param string $comment
     * @return $this
     */
    public function setOrderComment($comment)
    {
        return $this->setData(self::ORDER_COMMENT, $comment);
    }

    /**
     * Retrieve Order Comment Created At
     *
     * @api
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set Order Comment Created At
     *
     * @api
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->getData(self::CREATED_AT, $createdAt);
    }

    /**
     * Retrieve Order Comment Updated At
     *
     * @api
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set Order Comment Updated At
     *
     * @api
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->getData(self::UPDATED_AT, $updatedAt);
    }
}