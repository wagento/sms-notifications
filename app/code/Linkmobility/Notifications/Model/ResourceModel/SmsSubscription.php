<?php

namespace Linkmobility\Notifications\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\AbstractResource;

class SmsSubscription extends AbstractResource {

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sms_subscription', 'sms_subscription_id');
    }
}