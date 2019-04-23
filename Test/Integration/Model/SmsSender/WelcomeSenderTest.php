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

use Wagento\SMSNotifications\Model\SmsSender\WelcomeSender;
use Wagento\SMSNotifications\Test\Integration\SmsSenderTestCase;
use Magento\Customer\Model\Customer;

/**
 * Welcome SMS Sender Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 */
class WelcomeSenderTest extends SmsSenderTestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture smsMobileNumberFixtureProvider
     */
    public function testSendWelcomeSmsForCustomer(): void
    {
        $configMock = $this->getConfigMock();
        $welcomeSender = $this->objectManager->create(
            WelcomeSender::class,
            [
                'config' => $configMock,
                'messageService' => $this->getMessageServiceMock()
            ]
        );
        $customer = $this->objectManager->create(Customer::class)->load(1);

        $configMock->expects($this->once())
            ->method('getWelcomeMessageTemplate')
            ->with(1)
            ->willReturn('Welcome to our store!');

        $this->assertTrue($welcomeSender->send($customer));
    }
}
