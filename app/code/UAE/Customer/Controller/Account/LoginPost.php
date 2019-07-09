<?php
/**
 *
 * Modified By UAE
 *
 */

namespace UAE\Customer\Controller\Account;
class Index extends \Magento\Customer\Controller\Account
{
  /**
   * Login post action
   *
   * @return \Magento\Framework\Controller\Result\Redirect
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   */
  public function execute()
  {
      if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
          /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
          $resultRedirect = $this->resultRedirectFactory->create();
          $resultRedirect->setPath('*/*/');
          return $resultRedirect;
      }

      if ($this->getRequest()->isPost()) {
          $login = $this->getRequest()->getPost('login');
          if (!empty($login['username']) && !empty($login['password'])) {
              try {
                  $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                  $this->session->setCustomerDataAsLoggedIn($customer);
                  $this->session->regenerateId();
                  if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                      $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                      $metadata->setPath('/');
                      $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                  }
                  $redirectUrl = $this->accountRedirect->getRedirectCookie();
                  if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectUrl) {
                      $this->accountRedirect->clearRedirectCookie();
                      $resultRedirect = $this->resultRedirectFactory->create();
                      // URL is checked to be internal in $this->_redirect->success()
                      $resultRedirect->setUrl($this->_redirect->success($redirectUrl));
                      return $resultRedirect;
                  }
              } catch (EmailNotConfirmedException $e) {
                  $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                  $message = __(
                      'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                      $value
                  );
              } catch (UserLockedException $e) {
                  $message = __(
                      'Your email or password was entered incorrectly. Please try again.'
                  );
              } catch (AuthenticationException $e) {
                  $message = __('Your email or password was entered incorrectly. Please try again.');
              } catch (LocalizedException $e) {
                  $message = $e->getMessage();
              } catch (\Exception $e) {
                  // PA DSS violation: throwing or logging an exception here can disclose customer password
                  $this->messageManager->addError(
                      __('An unspecified error occurred. Please contact us for assistance.')
                  );
              } finally {
                  if (isset($message)) {
                      $this->messageManager->addError($message);
                      $this->session->setUsername($login['username']);
                  }
              }
          } else {
              $this->messageManager->addError(__('A login and a password are required.'));
          }
      }

      return $this->accountRedirect->getRedirect();
  }
    
}
