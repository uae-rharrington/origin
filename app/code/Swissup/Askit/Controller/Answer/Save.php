<?php
namespace Swissup\Askit\Controller\Answer;

class Save extends \Magento\Framework\App\Action\Action
{
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
     * @var \Swissup\Askit\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Askit\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Askit\Helper\Config $configHelper
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
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
        $isLoggedIn = $this->customerSession->isLoggedIn();
        $customer = $this->customerSession->getCustomer();

        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->redirectReferer();
            return;
        }

        try {
            $error = false;

            if (!\Zend_Validate::is(trim($post['text']), 'NotEmpty')) {
                $error = true;
            }

            if ($error) {
                throw new \Exception();
            }
            $question = $this->_objectManager->create('Swissup\Askit\Model\Message');
            $question->load($post['parent_id']);

            $post['item_id'] = $question->getItemId();
            $post['item_type_id'] = $question->getItemTypeId();
            $post['customer_id'] = $isLoggedIn ? $customer->getId() : null;

            $post['store_id'] = $this->storeManager->getStore()->getId();

            $post['status'] = $this->configHelper->getDefaultAnswerStatus();
            $post['hint'] = 0;
            if ($isLoggedIn) {
                $post['customer_name'] = $customer->getName();
                $post['email'] = $customer->getEmail();
            }

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
