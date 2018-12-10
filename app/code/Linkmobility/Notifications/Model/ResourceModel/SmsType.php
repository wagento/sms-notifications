<?php

namespace Linkmobility\Notifications\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\AbstractResource;

class SmsType extends AbstractResource {

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sms_type', 'sms_type_id');
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {
        // TODO: Implement getConnection() method.
    }
}