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

use Magento\Sales\Api\Data\OrderExtensionInterface;

$order = require __DIR__ . '/order.php';

/** @var \Magento\Sales\Api\Data\OrderExtensionInterface $orderExtensionAttributes */
$orderExtensionAttributes = $order->getExtensionAttributes() ?? $objectManager->create(OrderExtensionInterface::class);

$orderExtensionAttributes->setIsOrderHoldReleased(true);

$order->setExtensionAttributes($orderExtensionAttributes);
$order->save();

return $order;
