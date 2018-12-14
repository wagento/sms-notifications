<?php
namespace Linkmobility\Notifications\Controller\Customer;

class Config extends \Magento\Customer\Controller\AbstractAccount {

    public function execute() {

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}