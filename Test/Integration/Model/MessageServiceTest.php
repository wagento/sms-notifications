<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\Model;

use Wagento\SMSNotifications\Gateway\ApiClientInterface;
use Wagento\SMSNotifications\Gateway\Entity\SuccessResult;
use Wagento\SMSNotifications\Model\MessageService;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Message Service Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class MessageServiceTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store sms_notifications/api/source_type MSISDN
     * @magentoConfigFixture current_store sms_notifications/api/source +15555551234
     * @magentoConfigFixture current_store general/store_information/name Example Store
     *
     * @phpcs:disable Generic.Files.LineLength.TooLong
     */
    public function testSendMessage(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $messageService = $objectManager->create(
            MessageService::class,
            [
                'logger' => new \Psr\Log\Test\TestLogger(),
                'apiClient' => $this->getApiClientMock(),
            ]
        );

        $messageService->setOrder($this->getOrderMock());

        $message = 'Order #{{order_id}} has been placed at {{store_name}} by {{customer_name}}. View order: {{order_url}}';

        $this->assertTrue($messageService->sendMessage($message, '+15555555678', 'order'));
    }

    private function getApiClientMock(): MockObject
    {
        $apiClientMock = $this->getMockBuilder(ApiClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $result = new SuccessResult([
            'messageId' => 'ABC123',
            'resultCode' => 0,
            'description' => 'OK'
        ]);

        $apiClientMock->method('getResult')->willReturn($result);

        return $apiClientMock;
    }

    private function getOrderMock(): MockObject
    {
        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderMock->method('getId')->willReturn('1');
        $orderMock->method('getEntityId')->willReturn('1');
        $orderMock->method('getIncrementId')->willReturn('ORD1000001');
        $orderMock->method('getCustomerFirstname')->willReturn('John');
        $orderMock->method('getCustomerLastname')->willReturn('Smith');
        $orderMock->method('getStoreId')->willReturn('1');
        $orderMock->method('getStoreName')->willReturn("Main Website\nMain Website Store\n");
        $orderMock->method('getProtectCode')->willReturn('ABCDEF123456');

        return $orderMock;
    }
}
