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
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address as OrderAddress;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

require BP . '/dev/tests/integration/testsuite/Magento/Sales/_files/default_rollback.php';
require BP . '/dev/tests/integration/testsuite/Magento/Customer/_files/customer.php';
require __DIR__ . '/product_simple.php';

$addressData = include BP . '/dev/tests/integration/testsuite/Magento/Sales/_files/address_data.php';
$objectManager = Bootstrap::getObjectManager();
$billingAddress = $objectManager->create(OrderAddress::class, ['data' => $addressData]);

$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;

$shippingAddress->setId(null)->setAddressType('shipping');

/** @var \Magento\Sales\Model\Order\Payment $payment */
$payment = $objectManager->create(Payment::class);

$payment->setMethod('checkmo')
    ->setAdditionalInformation('last_trans_id', '11122')
    ->setAdditionalInformation(
        'metadata',
        [
            'type' => 'free',
            'fraudulent' => false,
        ]
    );

/** @var \Magento\Sales\Model\Order\Item $orderItem */
$orderItem = $objectManager->create(OrderItem::class);

$orderItem->setProductId($product->getId())
    ->setQtyOrdered(2)
    ->setBasePrice($product->getPrice())
    ->setPrice($product->getPrice())
    ->setRowTotal($product->getPrice())
    ->setProductType('simple');

/** @var \Magento\Sales\Model\Order $order */
$order = $objectManager->create(Order::class);

$order->setIncrementId('100000001')
    ->setState(Order::STATE_PROCESSING)
    ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING))
    ->setSubtotal(100)
    ->setGrandTotal(100)
    ->setBaseSubtotal(100)
    ->setBaseGrandTotal(100)
    ->setCustomerIsGuest(false)
    ->setCustomerId(1)
    ->setCustomerEmail($addressData['email'])
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId())
    ->addItem($orderItem)
    ->setPayment($payment);

$orderExtensionAttributes = $order->getExtensionAttributes() ?? $objectManager->create(OrderExtensionInterface::class);

$orderExtensionAttributes->setIsSmsNotificationSent(true);

$order->setExtensionAttributes($orderExtensionAttributes);

/** @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository */
$orderRepository = $objectManager->create(OrderRepositoryInterface::class);
$orderRepository->save($order);

return $order;
