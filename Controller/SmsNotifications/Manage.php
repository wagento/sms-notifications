<?php
namespace Linkmobility\Notifications\Controller\SmsNotifications;

class Manage extends \Magento\Customer\Controller\AbstractAccount {

    public function execute() {

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}