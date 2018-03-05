<?php
namespace Swissup\Askit\Model;

use Swissup\Askit\Api\Data\VoteInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Vote extends \Magento\Framework\Model\AbstractModel implements VoteInterface, IdentityInterface
{
    /**
     * cache tag
     */
    const CACHE_TAG = 'askit_vote';

    /**
     * @var string
     */
    protected $_cacheTag = 'askit_vote';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'askit_vote';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Swissup\Askit\Model\ResourceModel\Vote');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get id
     *
     * return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get message_id
     *
     * return int
     */
    public function getMessageId()
    {
        return $this->getData(self::MESSAGE_ID);
    }

    /**
     * Get customer_id
     *
     * return int
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set id
     *
     * @param int $id
     * return \Swissup\Askit\Api\Data\VoteInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set message_id
     *
     * @param int $messageId
     * return \Swissup\Askit\Api\Data\VoteInterface
     */
    public function setMessageId($messageId)
    {
        return $this->setData(self::ITEM_ID, $messageId);
    }

    /**
     * Set customer_id
     *
     * @param int $customerId
     * return \Swissup\Askit\Api\Data\VoteInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function isVoted($id, $customerId)
    {
        return $this->_getResource()->isVoted($id, $customerId);
    }
}
