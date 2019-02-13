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

use Linkmobility\Notifications\Model\SmsSender\CreditmemoSender;
use Linkmobility\Notifications\Test\Integration\SmsSenderTestCase;
use Magento\Sales\Model\Order;

/**
 * Credit Memo SMS Sender Test
 *
 * @package Linkmobility\Notifications\Test\Integration\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 */
class CreditmemoSenderTest extends SmsSenderTestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture creditmemoFixtureProvider
     * @magentoDataFixture smsSubscriptionsFixtureProvider
     * @magentoDataFixture smsMobileNumberFixtureProvider
     */
    public function testSendCreditmemoSmsForCustomer(): void
    {
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

        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        /** @var \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo */
        $creditmemo = $order->getCreditmemosCollection()->getFirstItem();

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

    public static function creditmemoFixtureProvider(): void
    {
        require __DIR__ . '/../../_files/creditmemo.php';
    }

    public static function guestCreditmemoFixtureProvider(): void
    {
        require __DIR__ . '/../../_files/creditmemo_guest.php';
    }
}
