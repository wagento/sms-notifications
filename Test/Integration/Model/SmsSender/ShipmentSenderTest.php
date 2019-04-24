<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\Model\SmsSender;

use Wagento\SMSNotifications\Model\SmsSender\ShipmentSender;
use Wagento\SMSNotifications\Test\Integration\SmsSenderTestCase;
use Magento\Sales\Api\Data\ShipmentExtensionInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order;

/**
 * Shipment SMS Sender Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ShipmentSenderTest extends SmsSenderTestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture smsSubscriptionsFixtureProvider
     * @magentoDataFixture smsMobileNumberFixtureProvider
     */
    public function testSendShipmentSmsForCustomer(): void
    {
        $shipment = $this->getShipmentFixture();
        $configMock = $this->getConfigMock();

        $configMock->expects($this->once())
            ->method('getOrderShippedTemplate')
            ->willReturn('Order shipped');

        $shipmentSender = $this->objectManager->create(
            ShipmentSender::class,
            [
                'config' => $configMock,
                'messageService' => $this->getMessageServiceMock()
            ]
        );

        $this->assertTrue($shipmentSender->send($shipment));
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture guestShipmentFixtureProvider
     */
    public function testSendShipmentSmsForGuest(): void
    {
        $shipmentSender = $this->objectManager->create(
            ShipmentSender::class,
            [
                'config' => $this->getConfigMock(),
                'messageService' => $this->getMessageServiceMock()
            ]
        );

        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        /** @var \Magento\Sales\Api\Data\ShipmentInterface $shipment */
        $shipment = $order->getShipmentsCollection()->getFirstItem();

        $this->assertFalse($shipmentSender->send($shipment));
    }

    public static function guestShipmentFixtureProvider(): void
    {
        require __DIR__ . '/../../_files/shipment_guest.php';
    }

    private function getShipmentFixture(): ShipmentInterface
    {
        $shipment = require __DIR__ . '/../../_files/shipment.php';
        $shipmentExtensionAttributes = $shipment->getExtensionAttributes()
            ?? $this->objectManager->create(ShipmentExtensionInterface::class);

        $shipmentExtensionAttributes->setIsSmsNotificationSent(false);

        $shipment->setExtensionAttributes($shipmentExtensionAttributes);

        return $shipment;
    }
}
