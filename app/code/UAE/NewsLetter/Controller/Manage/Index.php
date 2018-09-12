<?php
/**
 *
 * Modified By UAE
 * 
 */
namespace UAE\NewsLetter\Controller\Manage;

class Index extends \UAE\NewsLetter\Controller\Manage
{
    /**
     * Managing newsletter subscription page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Email Subscription'));
        $this->_view->renderLayout();
    }
}
