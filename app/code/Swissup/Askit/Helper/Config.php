<?php
namespace Swissup\Askit\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const DEFAULT_QUESTION_STATUS    = 'askit/general/defaultQuestionStatus';
    const DEFAULT_ANSWER_STATUS      = 'askit/general/defaultAnswerStatus';
    const ALLOWED_GUEST_QUESTION     = 'askit/general/allowedGuestQuestion';
    const ALLOWED_CUSTOMER_ANSWER    = 'askit/general/allowedCustomerAnswer';
    const ALLOWED_GUEST_ANSWER       = 'askit/general/allowedGuestAnswer';
    const ALLOWED_HINT               = 'askit/general/allowedHint';
    const ALLOWED_SHARE_CUSTOMERNAME = 'askit/general/shareCustomerName';
    const ALLOWED_SHARE_ITEM         = 'askit/general/shareItem';
    const ALLOWED_ENABLE_GRAVATAR    = 'askit/general/gravatar';
    const ALLOWED_ENABLE_NOQUESTIONS = 'askit/general/noquestions';

    protected function _getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    public function getDefaultQuestionStatus()
    {
        return $this->_getConfig(self::DEFAULT_QUESTION_STATUS);
    }

    public function getDefaultAnswerStatus()
    {
        return $this->_getConfig(self::DEFAULT_ANSWER_STATUS);
    }

    public function isAllowedGuestQuestion()
    {
        return (bool) $this->_getConfig(self::ALLOWED_GUEST_QUESTION);
    }

    public function isAllowedCustomerQuestion()
    {
        return (bool) $this->_getConfig(self::ALLOWED_CUSTOMER_ANSWER);
    }

    public function isAllowedGuestAnswer()
    {
        return (bool) $this->_getConfig(self::ALLOWED_GUEST_ANSWER);
    }

    public function isAllowedHint()
    {
        return (bool) $this->_getConfig(self::ALLOWED_HINT);
    }

    public function isAllowedShareCustomerName()
    {
        return (bool) $this->_getConfig(self::ALLOWED_SHARE_CUSTOMERNAME);
    }

    public function isAllowedShareItem()
    {
        return (bool) $this->_getConfig(self::ALLOWED_SHARE_ITEM);
    }

    public function isEnabledGravatar()
    {
        return (bool) $this->_getConfig(self::ALLOWED_ENABLE_GRAVATAR);
    }

    public function isEnabledNoQuestions()
    {
        return (bool) $this->_getConfig(self::ALLOWED_ENABLE_NOQUESTIONS);
    }
}
