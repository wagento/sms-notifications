<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Test\Integration\Model;

use LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface;
use LinkMobility\SMSNotifications\Model\ResourceModel\SmsSubscription as SmsSubscriptionResource;
use LinkMobility\SMSNotifications\Model\SmsSubscriptionFactory as SmsSubscriptionModelFactory;
use LinkMobility\SMSNotifications\Model\SmsSubscriptionRepository;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * SMS Subscription Repository Test
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsSubscriptionRepositoryTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;
    /**
     * @var \LinkMobility\SMSNotifications\Model\SmsSubscriptionRepository
     */
    private $smsSubscriptionRepository;
    /**
     * @var \LinkMobility\SMSNotifications\Model\SmsSubscription
     */
    private static $smsSubscriptionFixture;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture createSmsSubscriptionFixtureProvider
     */
    public function testGetReturnSmsSubscriptionEntity(): void
    {
        $result = $this->smsSubscriptionRepository->get((int)self::$smsSubscriptionFixture->getId());

        $this->assertInstanceOf(SmsSubscriptionInterface::class, $result);
        $this->assertEquals(self::$smsSubscriptionFixture->getDataModel()->__toArray(), $result->__toArray());
    }

    public function testGetThrowsNoSuchEntityException(): void
    {
        $this->expectException(NoSuchEntityException::class);

        $this->smsSubscriptionRepository->get(1000);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetListReturnsSearchResults(): void
    {
        self::createSmsSubscriptionsFromSourceFixtureProvider(7);

        $searchCriteria = $this->objectManager->create(SearchCriteriaBuilderFactory::class)
            ->create()
            ->addFilter('customer_id', 1)
            ->create();
        $results = $this->smsSubscriptionRepository->getList($searchCriteria);

        $this->assertInstanceOf(SearchResultsInterface::class, $results);
        $this->assertEquals(7, $results->getTotalCount());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSaveCreatesSmsSubscriptionEntity(): void
    {
        $smsSubscriptionEntity = $this->objectManager->create(SmsSubscriptionInterface::class)
            ->setCustomerId(1)
            ->setSmsType('order_placed');

        $result = $this->smsSubscriptionRepository->save($smsSubscriptionEntity);

        $this->assertInstanceOf(SmsSubscriptionInterface::class, $result);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture createSmsSubscriptionFixtureProvider
     */
    public function testSaveUpdatesSmsSubscriptionEntity(): void
    {
        $smsSubscriptionEntity = $this->objectManager->create(SmsSubscriptionInterface::class)
            ->setCustomerId(1)
            ->setSmsSubscriptionId((int)self::$smsSubscriptionFixture->getId())
            ->setSmsType('order_shipped');

        $result = $this->smsSubscriptionRepository->save($smsSubscriptionEntity);

        $this->assertEquals('order_shipped', $result->getSmsType());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture createSmsSubscriptionFixtureProvider
     */
    public function testDeleteRemovesSmsSubscriptionEntity(): void
    {
        $smsSubscriptionResource = $this->objectManager->create(SmsSubscriptionResource::class);
        /** @var \LinkMobility\SMSNotifications\Model\SmsSubscription $smsSubscriptionModel */
        $smsSubscriptionModel = $this->objectManager->create(SmsSubscriptionModelFactory::class)->create();

        $smsSubscriptionResource->load($smsSubscriptionModel, self::$smsSubscriptionFixture->getId());

        $result = $this->smsSubscriptionRepository->delete($smsSubscriptionModel->getDataModel());

        $this->assertTrue($result);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture createSmsSubscriptionFixtureProvider
     */
    public function testDeleteThrowsCouldNotDeleteException(): void
    {
        $this->expectException(CouldNotDeleteException::class);

        $mockSmsSubscriptionResource = $this->createMock(SmsSubscriptionResource::class);

        $mockSmsSubscriptionResource->method('delete')->will($this->throwException(new \Exception()));

        $smsSubscriptionEntity = $this->objectManager->create(SmsSubscriptionInterface::class);
        $smsSubscriptionRepository = $this->objectManager->create(
            SmsSubscriptionRepository::class,
            ['smsSubscriptionResourceModel' => $mockSmsSubscriptionResource]
        );

        $smsSubscriptionRepository->delete($smsSubscriptionEntity);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture createSmsSubscriptionFixtureProvider
     */
    public function testDeleteByIdRemovesSmsSubscriptionEntity(): void
    {
        $result = $this->smsSubscriptionRepository->deleteById((int)self::$smsSubscriptionFixture->getId());

        $this->assertTrue($result);
    }

    public static function createSmsSubscriptionFixtureProvider(): void
    {
        self::$smsSubscriptionFixture = require __DIR__ . '/../_files/create_sms_subscription.php';
    }

    public static function createSmsSubscriptionsFromSourceFixtureProvider(int $count = null): void
    {
        require __DIR__ . '/../_files/create_sms_subscriptions_from_source.php';
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->smsSubscriptionRepository = $this->objectManager->create(SmsSubscriptionRepository::class);
    }
}
