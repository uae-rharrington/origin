<?php
/**
 * Api Data Order Comment Interface
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */

namespace ClassyLlama\OrderComments\Api\Data;

/**
 * ClassyLlama\OrderComments\Api\Data\OrderCommentInterface
 *
 * @category ClassyLlama
 * @package ClassyLlama_OrderComments
 */
interface OrderCommentInterface
{
    /**
     * Order Comment Id Field Name
     */
    const ENTITY_ID = 'entity_id';

    /**
     * Order Id Field Name
     */
    const ORDER_ID = 'order_id';

    /**
     * Order Comment Type Field Name
     */
    const ORDER_TYPE = 'order_type';

    /**
     * Order Comment Field Name
     */
    const ORDER_COMMENT = 'order_comment';

    /**
     * Created At Field Name
     */
    const CREATED_AT = 'created_at';

    /**
     * Updated At Field Name
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Retrieve Order Comment Id
     *
     * @api
     * @return int|null
     */
    public function getId();

    /**
     * Set Order Comment Id
     *
     * @api
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Retrieve Order Id
     *
     * @api
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set Order Id
     *
     * @api
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Retrieve Order Comment Type
     *
     * @api
     * @return string|null
     */
    public function getOrderType();

    /**
     * Set Order Comment Type
     *
     * @api
     * @param string $type
     * @return $this
     */
    public function setOrderType($type);

    /**
     * Retrieve Order Comment
     *
     * @api
     * @return string|null
     */
    public function getOrderComment();

    /**
     * Set Order Comment
     *
     * @api
     * @param string $comment
     * @return $this
     */
    public function setOrderComment($comment);

    /**
     * Retrieve Order Comment Created At
     *
     * @api
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set Order Comment Created At
     *
     * @api
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Retrieve Order Comment Updated At
     *
     * @api
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set Order Comment Updated At
     *
     * @api
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}