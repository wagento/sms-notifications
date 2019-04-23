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

use Magento\Payment\Helper\Data as PaymentDataHelper;
use Magento\Sales\Api\Data\ShipmentExtensionInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\ShipmentFactory;

require BP . '/dev/tests/integration/testsuite/Magento/Sales/_files/default_rollback.php';
require __DIR__ . '/order.php';

$payment = $order->getPayment();
$paymentInfoBlock = $objectManager->get(PaymentDataHelper::class)->getInfoBlock($payment);
$items = [];

$payment->setBlockMock($paymentInfoBlock);

/** @var \Magento\Sales\Model\Order\Item $orderItem */
foreach ($order->getItems() as $orderItem) {
    $items[$orderItem->getId()] = $orderItem->getQtyOrdered();
}

/** @var \Magento\Sales\Api\Data\ShipmentInterface $shipment */
$shipment = $objectManager->get(ShipmentFactory::class)->create($order, $items);
$shipmentExtensionAttributes = $shipment->getExtensionAttributes()
    ?? $objectManager->create(ShipmentExtensionInterface::class);

$shipment->setPackages([['1'], ['2']]);
$shipment->setShipmentStatus(Shipment::STATUS_NEW);

$shipmentExtensionAttributes->setIsSmsNotificationSent(true);

$shipment->setExtensionAttributes($shipmentExtensionAttributes);
$shipment->save();

return $shipment;
