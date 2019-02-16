<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\_files
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

use LinkMobility\SMSNotifications\Model\SmsSubscription;
use LinkMobility\SMSNotifications\Model\Source\SmsType as SmsTypeSource;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$smsTypes = array_column((new SmsTypeSource())->toArray(), 'code');
$i = 0;
$count = $count ?? -1;
$createdSubscriptions = [];

foreach ($smsTypes as $smsType) {
    if ($i++ === $count) {
        break;
    }

    try {
        /** @var \LinkMobility\SMSNotifications\Model\SmsSubscription $smsSubscription */
        $smsSubscription = $objectManager->create(SmsSubscription::class);
        $smsSubscription->setData([
            'customer_id' => 1,
            'sms_type' => $smsType,
        ]);
        $smsSubscription->isObjectNew(true);
        $smsSubscription->save();
        $createdSubscriptions[] = $smsSubscription;
    } catch (\Exception $e) {
    }
}

return $createdSubscriptions;
