<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\Plugin\Sales\Model\Order
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\Plugin\Sales\Model\Order;

use Wagento\SMSNotifications\Model\SmsSender\InvoiceSender;
use Wagento\SMSNotifications\Plugin\Sales\Model\Order\InvoicePlugin;
use Wagento\SMSNotifications\Test\Integration\_stubs\Model\SmsSender;
use Magento\Framework\Interception\PluginList\PluginList;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice as InvoiceModel;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Invoice Plug-in Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Plugin\Sales\Model\Order
 * @author Joseph Leedy <joseph@wagento.com>
 */
class InvoicePluginTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    public function testPluginIsConfigured(): void
    {
        $pluginList = $this->objectManager->create(PluginList::class);
        $plugins = $pluginList->get(InvoiceModel::class, []);

        $this->assertArrayHasKey('sms_notifications_send_order_invoiced_sms', $plugins);
        $this->assertSame(InvoicePlugin::class, $plugins['sms_notifications_send_order_invoiced_sms']['instance']);
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDbIsolation enabled
     * @magentoDataFixture orderDataFixtureProvider
     */
    public function testAfterRegisterSendsInvoiceSms(): void
    {
        $smsSenderMock = $this->getMockBuilder(SmsSender::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $order = $this->objectManager->create(Order::class);
        $orderService = $this->objectManager->create(InvoiceManagementInterface::class);

        $order->loadByIncrementId('100000001');

        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $orderService->prepareInvoice($order);

        $smsSenderMock->expects($this->once())->method('send')->with($invoice)->willReturn(true);

        $this->objectManager->configure([InvoiceSender::class => ['shared' => true]]);
        $this->objectManager->addSharedInstance($smsSenderMock, InvoiceSender::class);

        $invoice->register();
    }

    public static function orderDataFixtureProvider(): void
    {
        require __DIR__ . '/../../../../_files/order.php';
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
