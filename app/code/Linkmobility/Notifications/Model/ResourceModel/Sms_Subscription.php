<?php

namespace Linkmobility\Notifications\Model\ResourceModel;

class Sms_Subscription extends \Magento\Rule\Model\ResourceModel\AbstractResource
{

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