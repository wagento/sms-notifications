<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Model\ResourceModel\SmsSubscription
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Model\ResourceModel\SmsSubscription;

use Linkmobility\Notifications\Model\ResourceModel\SmsSubscription as SmsSubscriptionResourceModel;
use Linkmobility\Notifications\Model\SmsSubscription as SmsSubscriptionModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * SMS Subscription Collection
 *
 * @package Linkmobility\Notifications\Model\ResourceModel\SmsSubscription
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(SmsSubscriptionModel::class, SmsSubscriptionResourceModel::class);
    }
}
