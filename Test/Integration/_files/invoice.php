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

use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\InvoiceManagementInterface;

require BP . '/dev/tests/integration/testsuite/Magento/Sales/_files/default_rollback.php';
require __DIR__ . '/order.php';

/** @var \Magento\Framework\ObjectManagerInterface $objectManager */

$orderService = $objectManager->create(InvoiceManagementInterface::class);
/** @var \Magento\Sales\Model\Order\Invoice $invoice */
$invoice = $orderService->prepareInvoice($order);

$invoice->register();

/** @var \Magento\Sales\Model\Order $order */
$order = $invoice->getOrder();

$order->setIsInProcess(true);

/** @var \Magento\Framework\DB\Transaction $transactionSave */
$transactionSave = $objectManager->create(Transaction::class);

$transactionSave->addObject($invoice)->addObject($order)->save();
