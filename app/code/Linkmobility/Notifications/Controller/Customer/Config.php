<?php
namespace Magecomp\Mycard\Controller\Customer;

class Config extends \Magento\Framework\App\Action\Action {

    public function execute() {

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}