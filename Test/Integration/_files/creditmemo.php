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

use Magento\Sales\Api\Data\CreditmemoExtensionInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\CreditmemoFactory;

require BP . '/dev/tests/integration/testsuite/Magento/Sales/_files/default_rollback.php';
require __DIR__ . '/order.php';

/** @var \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory */
$creditmemoFactory = $objectManager->get(CreditmemoFactory::class);
/** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
$creditmemo = $creditmemoFactory->createByOrder($order, $order->getData());
$creditmemoExtensionAttributes = $creditmemo->getExtensionAttributes()
    ?? $objectManager->create(CreditmemoExtensionInterface::class);

$creditmemo->setOrder($order);
$creditmemo->setState(Creditmemo::STATE_OPEN);
$creditmemo->setIncrementId('100000001');

$creditmemoExtensionAttributes->setIsSmsNotificationSent(true);

$creditmemo->setExtensionAttributes($creditmemoExtensionAttributes);
$creditmemo->save();

/** @var \Magento\Sales\Model\Order\Item $orderItem */
$orderItem = current($order->getAllItems());

$orderItem->setName('Test item')
    ->setQtyRefunded(1)
    ->setQtyInvoiced(10)
    ->setOriginalPrice(20);

/** @var \Magento\Sales\Model\Order\Creditmemo\Item $creditItem */
$creditItem = $objectManager->get(\Magento\Sales\Model\Order\Creditmemo\Item::class);

$creditItem->setCreditmemo($creditmemo)
    ->setName('Creditmemo item')
    ->setOrderItemId($orderItem->getId())
    ->setQty(1)
    ->setPrice(20)
    ->save();

return $creditmemo;
