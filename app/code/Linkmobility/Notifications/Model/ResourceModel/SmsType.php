<?php

namespace Linkmobility\Notifications\Model\ResourceModel;

class SmsType extends \Magento\Rule\Model\ResourceModel\AbstractResource
{

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sms_type', 'sms_type_id');
    }
}