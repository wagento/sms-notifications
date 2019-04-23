<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription;

use Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription as SmsSubscriptionResourceModel;
use Wagento\SMSNotifications\Model\SmsSubscription as SmsSubscriptionModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * SMS Subscription Collection
 *
 * @package Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'sms_subscription_id';

    /**
     * @return string[]
     */
    public function getAllSmsTypes(): array
    {
        $smsTypesSelect = clone $this->getSelect();

        $smsTypesSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $smsTypesSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $smsTypesSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $smsTypesSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $smsTypesSelect->columns('sms_type', 'main_table');

        return $this->getConnection()->fetchCol($smsTypesSelect, $this->_bindParams);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(SmsSubscriptionModel::class, SmsSubscriptionResourceModel::class);
    }
}
