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

use Wagento\SMSNotifications\Model\SmsSender\InvoiceSender;
use Wagento\SMSNotifications\Test\Integration\SmsSenderTestCase;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;

/**
 * Invoice SMS Sender Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 */
class InvoiceSenderTest extends SmsSenderTestCase
{
    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture smsSubscriptionsFixtureProvider
     * @magentoDataFixture smsMobileNumberFixtureProvider
     * @magentoDataFixture customerInvoiceFixtureProvider
     */
    public function testSendInvoiceSmsForCustomer(): void
    {
        $configMock = clone $this->getConfigMock();

        $configMock->expects($this->once())
            ->method('getOrderInvoicedTemplate')
            ->willReturn('Order invoiced');

        $invoiceSender = $this->objectManager->create(
            InvoiceSender::class,
            [
                'config' => $configMock,
                'messageService' => $this->getMessageServiceMock()
            ]
        );

        $this->assertTrue($invoiceSender->send($this->getInvoice()));
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture guestInvoiceFixtureProvider
     */
    public function testSendInvoiceSmsForGuest(): void
    {
        $invoiceSender = $this->objectManager->create(
            InvoiceSender::class,
            [
                'config' => $this->getConfigMock(),
                'messageService' => $this->getMessageServiceMock()
            ]
        );

        $this->assertFalse($invoiceSender->send($this->getInvoice()));
    }

    public static function customerInvoiceFixtureProvider(): void
    {
        require __DIR__ . '/../../_files/invoice.php';
    }

    public static function guestInvoiceFixtureProvider(): void
    {
        require __DIR__ . '/../../_files/invoice_guest.php';
    }

    private function getInvoice(): Invoice
    {
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');

        return $order->getInvoiceCollection()->getFirstItem();
    }
}
