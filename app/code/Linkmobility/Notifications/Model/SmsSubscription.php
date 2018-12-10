<?php

namespace Linkmobility\Notifications\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Sms_Subscription
 * @package Linkmobility\Notifications\Model
 *
 * @method int|null getSmsSubscriptionId()
 * @method Sms_Subscription setSmsSubscriptionId(int $id)
 */
class SmsSubscription extends AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'linkmobility_notifications';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'sms_subscription';

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Linkmobility\Notifications\Model\ResourceModel\SmsSubscription');
        $this->setIdFieldName('sms_subscription_id');
    }
    
}