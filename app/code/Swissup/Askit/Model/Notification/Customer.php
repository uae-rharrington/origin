<?php

namespace Swissup\Askit\Model\Notification;

class Customer extends NotificationAbstract
{
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $question = $observer->getEvent()->getMessage();
        $rawData = $question->getData();

        $id = $request->getParam('id');
        $question->load($id);

        $store = $this->storeManager->getStore($question->getStoreId());

        $area = \Magento\Framework\App\Area::AREA_FRONTEND;
        $this->appEmulation->startEnvironmentEmulation($store->getId(), $area);

        $from = $this->scopeConfig->getValue(
            self::ASKIT_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
        $to = ['email' => $question->getEmail(), 'name' => $question->getCustomerName()];

        $templateId = $this->scopeConfig->getValue(
            self::ASKIT_CUSTOMER_NOTIFICATION_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
        $_item = $this->urlHelper->get($question->getItemTypeId(), $question->getItemId(), false);

        $vars = [
            'user_name' => $question->getCustomerName(),
            'item_name' => $_item['label'],
            'item_url'  => $_item['href'],
            'question'  => $question->getText(),
            'answer'    => $rawData['answer'],
        ];
        $this->_sendEmail($from, $to, $templateId, $vars, $store, $area);

        $this->appEmulation->stopEnvironmentEmulation();

        return $this;
    }
}
