<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Test\Integration\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Test\Integration\Plugin\Sales\Api;

use Linkmobility\Notifications\Model\SmsSender\CreditmemoSender;
use Linkmobility\Notifications\Plugin\Sales\Api\CreditmemoRepositoryInterfacePlugin;
use Linkmobility\Notifications\Test\Integration\_stubs\Model\SmsSender;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Interception\PluginList\PluginList;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Credit Memo Repository Interface Plugin Test
 *
 * @package Linkmobility\Notifications\Test\Integration\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 */
class CreditmemoRepositoryInterfacePluginTest extends TestCase
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
        $plugins = $pluginList->get(CreditmemoRepositoryInterface::class, []);

        $this->assertArrayHasKey('sms_notifications_send_refund_sms', $plugins);
        $this->assertSame(
            CreditmemoRepositoryInterfacePlugin::class,
            $plugins['sms_notifications_send_refund_sms']['instance']
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture creditmemoDataFixtureProvider
     */
    public function testAfterSaveSendsOrderSms(): void
    {
        $smsSenderMock = $this->getMockBuilder(SmsSender::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $creditmemoRepository = $this->objectManager->create(CreditmemoRepositoryInterface::class);
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('increment_id', '100000001')->create();
        $searchResults = $creditmemoRepository->getList($searchCriteria);
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = current($searchResults->getItems());

        $smsSenderMock->expects($this->once())->method('send')->with($creditmemo)->willReturn(true);

        $this->objectManager->configure([CreditmemoSender::class => ['shared' => true]]);
        $this->objectManager->addSharedInstance($smsSenderMock, CreditmemoSender::class);

        $creditmemo->addComment('test');

        $creditmemoRepository->save($creditmemo);
    }

    public static function creditmemoDataFixtureProvider(): void
    {
        require __DIR__ . '/../../../_files/creditmemo.php';
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
    }
}
