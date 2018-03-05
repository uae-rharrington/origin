<?php
namespace Swissup\Askit\Controller\Vote;

use Swissup\Askit\Api\Data\MessageInterface;

class Dec extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
    }

    protected function redirectReferer()
    {
        $this->_redirect($this->_redirect->getRedirectUrl());
    }

    /**
     * Post user question
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        if (!$id || !$this->customerSession->isLoggedIn()) {
            $this->messageManager->addError(
                __('Sorry, only logged in customer can hint.')
            );
            $this->redirectReferer();
            return;
        }

        try {
            $customerId = $this->customerSession->getId();

            $modelVote = $this->_objectManager->create('Swissup\Askit\Model\Vote');
            if ($modelVote->isVoted($id, $customerId)) {
                $this->messageManager->addError(
                    __('Sorry, already voted')
                );
                $this->redirectReferer();
                return;
            }

            $modelMessage = $this->_objectManager->create('Swissup\Askit\Model\Message');
            $modelMessage->load($id);

            $modelMessage->setHint($modelMessage->getHint() - 1);
            $modelMessage->save();

            $modelVote->setData([
                'message_id' => $modelMessage->getId(),
                'customer_id' => $customerId
            ])->save();

            $this->redirectReferer();
            return;
        } catch (\Exception $e) {
            // $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->redirectReferer();
            return;
        }
    }
}
