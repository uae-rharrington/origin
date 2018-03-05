<?php
namespace Swissup\Askit\Model\ResourceModel\Answer\Collection;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     *
     * @return \Swissup\Askit\Model\ResourceModel\Answer\Collection
     */
    public function create()
    {
        return $this->_objectManager->create('Swissup\Askit\Model\ResourceModel\Answer\Collection');
    }
}
