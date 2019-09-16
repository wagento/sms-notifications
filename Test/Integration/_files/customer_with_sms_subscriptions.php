<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

require BP . '/dev/tests/integration/testsuite/Magento/Customer/_files/customer.php';

/** @var \Magento\Customer\Api\Data\CustomerInterface $customer */

$customer->getExtensionAttributes()->setSmsNotificationSubscriptions([
    'order_placed',
    'order_updated',
    'order_shipped',
]);

return $customer;
