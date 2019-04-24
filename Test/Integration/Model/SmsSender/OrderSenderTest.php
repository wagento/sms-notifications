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

use Wagento\SMSNotifications\Model\SmsSender\OrderSender;
use Wagento\SMSNotifications\Test\Integration\SmsSenderTestCase;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Order SMS Sender Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 */
class OrderSenderTest extends SmsSenderTestCase
{
    /**
     * @dataProvider orderSmsSenderDataProvider
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture smsSubscriptionsFixtureProvider
     * @magentoDataFixture smsMobileNumberFixtureProvider
     */
    public function testSendOrderSmsForCustomer(?string $fixtureType, string $configMethod, string $template): void
    {
        $order = $this->getOrderFixture($fixtureType);
        $configMock = $this->getConfigMock();

        $configMock->expects($this->once())
            ->method($configMethod)
            ->willReturn($template);

        $orderSender = $this->objectManager->create(
            OrderSender::class,
            [
                'config' => $configMock,
                'messageService' => $this->getMessageServiceMock()
            ]
        );

        $this->assertTrue($orderSender->send($order));
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testSendOrderSmsForGuest(): void
    {
        $order = $this->getOrderFixture('guest');
        $configMock = $this->getConfigMock();
        $orderSender = $this->objectManager->create(
            OrderSender::class,
            [
                'config' => $configMock,
                'messageService' => $this->getMessageServiceMock()
            ]
        );

        $this->assertFalse($orderSender->send($order));
    }

    public static function orderSmsSenderDataProvider(): array
    {
        return [
            'order_placed_new' => [
                'type' => 'new',
                'configMethod' => 'getOrderPlacedTemplate',
                'template' => 'Order placed (state: new)'
            ],
            'order_placed_processing' => [
                'type' => null,
                'configMethod' => 'getOrderPlacedTemplate',
                'template' => 'Order placed (state: processing)'
            ],
            'order_canceled' => [
                'type' => 'canceled',
                'configMethod' => 'getOrderCanceledTemplate',
                'template' => 'Order canceled'
            ],
            'order_held' => [
                'type' => 'holded',
                'configMethod' => 'getOrderHeldTemplate',
                'template' => 'Order held'
            ],
            'order_released' => [
                'type' => 'released',
                'configMethod' => 'getOrderReleasedTemplate',
                'template' => 'Order hold released'
            ],
        ];
    }

    private function getOrderFixture(?string $fixtureType = null): OrderInterface
    {
        $path = __DIR__ . '/../../_files/order';

        if ($fixtureType !== null) {
            $path .= '_' . $fixtureType;
        }

        $path .= '.php';

        $order = require $path;
        $orderExtensionAttributes = $order->getExtensionAttributes()
            ?? $this->objectManager->create(OrderExtensionInterface::class);

        $orderExtensionAttributes->setIsSmsNotificationSent(false);

        $order->setExtensionAttributes($orderExtensionAttributes);

        return $order;
    }
}
