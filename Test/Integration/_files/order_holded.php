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

use Magento\Sales\Model\Order;

$order = require __DIR__ . '/order.php';

$order->setState(Order::STATE_HOLDED);
$order->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_HOLDED));
$order->save();

return $order;
