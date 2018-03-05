<?php
namespace Swissup\Askit\Model\Message;

use Magento\Framework\ObjectManagerInterface;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    protected $messageClass = 'Swissup\Askit\Model\Message';

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     *
     * @return \Swissup\Askit\Model\Message
     */
    public function create()
    {
        return $this->objectManager->create($this->messageClass);
    }
}
