<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\etc\frontend
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Test\Integration\etc\frontend;

use LinkMobility\SMSNotifications\Observer\CustomerRegisterSuccessObserver;
use Magento\Framework\Event\ConfigInterface as EventObserverConfig;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Event Observer Configuration Test
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\etc\frontend
 * @author Joseph Leedy <joseph@wagento.com>
 */
class EventObserverConfigurationTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     */
    public function testCustomerRegisterSuccessEventObserverIsConfigured()
    {
        /** @var \Magento\Framework\Event\ConfigInterface $observerConfig */
        $observerConfig = ObjectManager::getInstance()->create(EventObserverConfig::class);
        $observers = $observerConfig->getObservers('customer_register_success');

        $this->assertArrayHasKey('sms_notifications_save_subscriptions', $observers);
        $this->assertSame(
            ltrim(CustomerRegisterSuccessObserver::class, '\\'),
            $observers['sms_notifications_save_subscriptions']['instance']
        );
    }
}
