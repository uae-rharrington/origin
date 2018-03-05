<?php
namespace Swissup\Askit\Controller\Question;

use Swissup\Askit\Api\Data\MessageInterface;
use Magento\Store\Model\ScopeInterface;

class Save extends \Magento\Framework\App\Action\Action
{
    const ASKIT_DEFAULT_QUESTION_STATUS = 'askit/general/defaultQuestionStatus';
    const ASKIT_ALLOWED_GUEST_QUESTION  = 'askit/general/allowedGuestQuestion';

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    protected function redirectReferer()
    {
        $this->_redirect($this->_redirect->getRedirectUrl());
    }

    protected function getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Post user question
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $this->redirectReferer();
            return;
        }

        $isLoggedIn = $this->customerSession->isLoggedIn();
        $customer = $this->customerSession->getCustomer();
        $isAllowedGuestQuestion = $this->getConfig(self::ASKIT_ALLOWED_GUEST_QUESTION);

        if (!$isLoggedIn && !$isAllowedGuestQuestion) {
            $this->messageManager->addError(__('Your must login'));
            $this->redirectReferer();
            return;
        }

        try {
            $error = false;

            if (!\Zend_Validate::is(trim($post['customer_name']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['text']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                $error = true;
            }

            if ($error) {
                throw new \Exception();
            }
            $post['customer_id'] = $isLoggedIn ? $customer->getId() : null;

            $post['store_id'] = $this->storeManager->getStore()->getId();

            $post['status'] = $this->getConfig(self::ASKIT_DEFAULT_QUESTION_STATUS);

            $model = $this->_objectManager->create('Swissup\Askit\Model\Message');

            $model->setData($post);

            $model->save();

            $this->_eventManager->dispatch(
                'askit_message_after_save',
                ['message' => $model, 'request' => $this->getRequest()]
            );

            $this->messageManager->addSuccess(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );
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
