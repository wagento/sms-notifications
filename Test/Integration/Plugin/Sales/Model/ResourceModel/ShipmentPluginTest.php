<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\Plugin\Sales\Model\ResourceModel
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\Plugin\Sales\Model\ResourceModel;

use Wagento\SMSNotifications\Model\SmsSender\ShipmentSender;
use Wagento\SMSNotifications\Plugin\Sales\Model\ResourceModel\Order\ShipmentPlugin;
use Wagento\SMSNotifications\Test\Integration\_stubs\Model\SmsSender;
use Magento\Framework\Interception\PluginList\PluginList;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResourceModel;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Shipment Plug-in Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Plugin\Sales\Model\ResourceModel
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ShipmentPluginTest extends TestCase
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
        $plugins = $pluginList->get(ShipmentResourceModel::class, []);

        $this->assertArrayHasKey('sms_notifications_send_shipment_sms', $plugins);
        $this->assertSame(ShipmentPlugin::class, $plugins['sms_notifications_send_shipment_sms']['instance']);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture shipmentDataFixtureProvider
     */
    public function testAfterSaveSendsShipmentSms(): void
    {
        $smsSenderMock = $this->getMockBuilder(SmsSender::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $order = $this->objectManager->create(Order::class);

        $order->loadByIncrementId('100000001');

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $order->getShipmentsCollection()->getFirstItem();

        $smsSenderMock->expects($this->once())->method('send')->with($shipment)->willReturn(true);

        $this->objectManager->configure([ShipmentSender::class => ['shared' => true]]);
        $this->objectManager->addSharedInstance($smsSenderMock, ShipmentSender::class);

        $shipment->setCustomerNote('test');
        $shipment->setCustomerNoteNotify(false);
        $shipment->save();
    }

    public static function shipmentDataFixtureProvider(): void
    {
        require __DIR__ . '/../../../../_files/shipment.php';
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->objectManager = null;
    }
}
