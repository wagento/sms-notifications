<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\Plugin\Sales\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Test\Integration\Plugin\Sales\Model;

use LinkMobility\SMSNotifications\Plugin\Sales\Model\OrderPlugin;
use Magento\Framework\Interception\PluginList\PluginList;
use Magento\Sales\Model\Order;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Order Plug-in Test
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\Plugin\Sales\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class OrderPluginTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @magentoAppArea frontend
     */
    public function testPluginIsConfigured(): void
    {
        $pluginList = $this->objectManager->create(PluginList::class);
        $plugins = $pluginList->get(Order::class, []);

        $this->assertArrayHasKey('sms_notifications_add_order_extension_attribute', $plugins);
        $this->assertSame(OrderPlugin::class, $plugins['sms_notifications_add_order_extension_attribute']['instance']);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture orderDataFixtureProvider
     */
    public function testAfterUnholdSetsExtensionAttribute(): void
    {
        $order = $this->objectManager->create(Order::class);

        $order->loadByIncrementId('100000001');
        $order->hold();
        $order->unhold();

        $this->assertTrue($order->getExtensionAttributes()->getIsOrderHoldReleased());
    }

    public static function orderDataFixtureProvider(): void
    {
        require __DIR__ . '/../../../_files/order.php';
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
    }
}
