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

use Wagento\SMSNotifications\Model\SmsSender\CreditmemoSender;
use Wagento\SMSNotifications\Test\Integration\SmsSenderTestCase;
use Magento\Sales\Api\Data\CreditmemoExtensionInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\Order;

/**
 * Credit Memo SMS Sender Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 */
class CreditmemoSenderTest extends SmsSenderTestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture smsSubscriptionsFixtureProvider
     * @magentoDataFixture smsMobileNumberFixtureProvider
     */
    public function testSendCreditmemoSmsForCustomer(): void
    {
        $creditmemo = $this->getCreditmemoFixture();
        $configMock = $this->getConfigMock();

        $configMock->expects($this->once())
            ->method('getOrderRefundedTemplate')
            ->willReturn('Order refunded');

        $creditmemoSender = $this->objectManager->create(
            CreditmemoSender::class,
            [
                'config' => $configMock,
                'messageService' => $this->getMessageServiceMock()
            ]
        );

        $this->assertTrue($creditmemoSender->send($creditmemo));
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture guestCreditmemoFixtureProvider
     */
    public function testSendCreditmemoSmsForGuest(): void
    {
        $creditmemoSender = $this->objectManager->create(
            CreditmemoSender::class,
            [
                'config' => $this->getConfigMock(),
                'messageService' => $this->getMessageServiceMock()
            ]
        );

        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        /** @var \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo */
        $creditmemo = $order->getCreditmemosCollection()->getFirstItem();

        $this->assertFalse($creditmemoSender->send($creditmemo));
    }

    public static function guestCreditmemoFixtureProvider(): void
    {
        require __DIR__ . '/../../_files/creditmemo_guest.php';
    }

    private function getCreditmemoFixture(): CreditmemoInterface
    {
        $creditmemo = require __DIR__ . '/../../_files/creditmemo.php';
        $creditmemoExtensionAttributes = $creditmemo->getExtensionAttributes()
            ?? $this->objectManager->create(CreditmemoExtensionInterface::class);

        $creditmemoExtensionAttributes->setIsSmsNotificationSent(false);

        $creditmemo->setExtensionAttributes($creditmemoExtensionAttributes);

        return $creditmemo;
    }
}
