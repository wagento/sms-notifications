<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Test\Integration\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Test\Integration\Model\SmsSender;

use Linkmobility\Notifications\Model\SmsSender\ShipmentSender;
use Linkmobility\Notifications\Test\Integration\SmsSenderTestCase;
use Magento\Sales\Model\Order;

/**
 * Shipment SMS Sender Test
 *
 * @package Linkmobility\Notifications\Test\Integration\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ShipmentSenderTest extends SmsSenderTestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture shipmentFixtureProvider
     * @magentoDataFixture smsSubscriptionsFixtureProvider
     * @magentoDataFixture smsMobileNumberFixtureProvider
     */
    public function testSendShipmentSmsForCustomer(): void
    {
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

        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        /** @var \Magento\Sales\Api\Data\ShipmentInterface $shipment */
        $shipment = $order->getShipmentsCollection()->getFirstItem();

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

    public static function shipmentFixtureProvider(): void
    {
        require __DIR__ . '/../../_files/shipment.php';
    }

    public static function guestShipmentFixtureProvider(): void
    {
        require __DIR__ . '/../../_files/shipment_guest.php';
    }
}
