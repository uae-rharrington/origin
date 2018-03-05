<?php

namespace Swissup\SoldTogether\Model;

use Swissup\SoldTogether\Api\Data\EntityInterface;
use Magento\Framework\DataObject\IdentityInterface;

abstract class AbstractModel extends \Magento\Framework\Model\AbstractModel
    implements EntityInterface, IdentityInterface
{

    public function relationExist($productId, $relatedId, $storeId)
    {
        return $this->_getResource()->relationExist($productId, $relatedId, $storeId);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [$this->_cacheTag . '_' . $this->getId()];
    }

    /**
     * Get relation_id
     *
     * return int
     */
    public function getRelationId()
    {
        return $this->getData(self::RELATION_ID);
    }

    /**
     * Get product_id
     *
     * return int
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Get related_id
     *
     * return int
     */
    public function getRelatedId()
    {
        return $this->getData(self::RELATED_ID);
    }

    /**
     * Get store_id
     *
     * return int
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get product_name
     *
     * return string
     */
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * Get related_name
     *
     * return string
     */
    public function getRelatedName()
    {
        return $this->getData(self::RELATED_NAME);
    }

    /**
     * Get weight
     *
     * return int
     */
    public function getWeight()
    {
        return $this->getData(self::WEIGHT);
    }

    /**
     * Get is_admin
     *
     * return int
     */
    public function getIsAdmin()
    {
        return $this->getData(self::IS_ADMIN);
    }

    /**
     * Set relation_id
     *
     * @param int $relationId
     * return \Swissup\SoldTogether\Api\Data\EntityInterface
     */
    public function setRelationId($relationId)
    {
        return $this->setData(self::RELATION_ID, $relationId);
    }

    /**
     * Set product_id
     *
     * @param int $productId
     * return \Swissup\SoldTogether\Api\Data\EntityInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Set related_id
     *
     * @param int $relatedId
     * return \Swissup\SoldTogether\Api\Data\EntityInterface
     */
    public function setRelatedId($relatedId)
    {
        return $this->setData(self::RELATED_ID, $relatedId);
    }

    /**
     * Set store_id
     *
     * @param int $storeId
     * return \Swissup\SoldTogether\Api\Data\EntityInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Set product_name
     *
     * @param int $productName
     * return \Swissup\SoldTogether\Api\Data\EntityInterface
     */
    public function setProductName($productName)
    {
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    /**
     * Set related_name
     *
     * @param int $relatedName
     * return \Swissup\SoldTogether\Api\Data\EntityInterface
     */
    public function setRelatedName($relatedName)
    {
        return $this->setData(self::RELATED_NAME, $relatedName);
    }

    /**
     * Set weight
     *
     * @param int $weight
     * return \Swissup\SoldTogether\Api\Data\EntityInterface
     */
    public function setWeight($weight)
    {
        return $this->setData(self::WEIGHT, $weight);
    }

    /**
     * Set is_admin
     *
     * @param int $isAdmin
     * return \Swissup\SoldTogether\Api\Data\EntityInterface
     */
    public function setIsAdmin($isAdmin)
    {
        return $this->setData(self::IS_ADMIN, $isAdmin);
    }
}
