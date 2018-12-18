<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace Linkmobility\Notifications\Test\Integration\Model;

use Linkmobility\Notifications\Api\Data\SmsSubscriptionInterface;
use LinkMobility\Notifications\Model\ResourceModel\SmsSubscription as SmsSubscriptionResource;
use Linkmobility\Notifications\Model\SmsSubscriptionFactory as SmsSubscriptionModelFactory;
use Linkmobility\Notifications\Model\SmsSubscriptionRepository;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * SMS Subscription Repository Test
 *
 * @package Linkmobility\Notifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsSubscriptionRepositoryTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;
    /**
     * @var \Linkmobility\Notifications\Model\SmsSubscriptionRepository
     */
    private $smsSubscriptionRepository;
    /**
     * @var \Linkmobility\Notifications\Model\SmsSubscription
     */
    private static $smsSubscriptionFixture;

    /**
     * @magentoDataFixture createSmsSubscriptionFixtureProvider
     */
    public function testGetReturnSmsSubscriptionEntity()
    {
        $result = $this->smsSubscriptionRepository->get(self::$smsSubscriptionFixture->getId());

        $this->assertInstanceOf(SmsSubscriptionInterface::class, $result);
        $this->assertEquals(self::$smsSubscriptionFixture->getDataModel()->__toArray(), $result->__toArray());
    }

    public function testGetThrowsNoSuchEntityException()
    {
        $this->expectException(NoSuchEntityException::class);

        $this->smsSubscriptionRepository->get(1000);
    }

    /**
     * @magentoDataFixture createDisabledSubscriptionsFixtureProvider
     */
    public function testGetListReturnsSearchResults()
    {
        $searchCriteria = $this->objectManager->create(SearchCriteriaBuilderFactory::class)
            ->create()
            ->addFilter('is_active', 0)
            ->create();
        $results = $this->smsSubscriptionRepository->getList($searchCriteria);

        $this->assertInstanceOf(SearchResultsInterface::class, $results);
        $this->assertEquals(10, $results->getTotalCount());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveCreatesSmsSubscriptionEntity()
    {
        $smsSubscriptionEntity = $this->objectManager->create(SmsSubscriptionInterface::class)
            ->setCustomerId('1')
            ->setSmsTypeId(1)
            ->setIsActive(true);

        $result = $this->smsSubscriptionRepository->save($smsSubscriptionEntity);

        $this->assertInstanceOf(SmsSubscriptionInterface::class, $result);
    }

    /**
     * @magentoDataFixture createSmsSubscriptionFixtureProvider
     */
    public function testSaveUpdatesSmsSubscriptionEntity()
    {
        $smsSubscriptionEntity = $this->objectManager->create(SmsSubscriptionInterface::class)
            ->setSmsSubscriptionId(self::$smsSubscriptionFixture->getId())
            ->setIsActive(false);

        $result = $this->smsSubscriptionRepository->save($smsSubscriptionEntity);

        $this->assertFalse($result->getIsActive());
    }

    /**
     * @magentoDataFixture createSmsSubscriptionFixtureProvider
     */
    public function testDeleteRemovesSmsSubscriptionEntity()
    {
        $smsSubscriptionResource = $this->objectManager->create(SmsSubscriptionResource::class);
        /** @var \Linkmobility\Notifications\Model\SmsSubscription $smsSubscriptionModel */
        $smsSubscriptionModel = $this->objectManager->create(SmsSubscriptionModelFactory::class)->create();

        $smsSubscriptionResource->load($smsSubscriptionModel, self::$smsSubscriptionFixture->getId());

        $result = $this->smsSubscriptionRepository->delete($smsSubscriptionModel->getDataModel());

        $this->assertTrue($result);
    }

    /**
     * @magentoDataFixture createSmsSubscriptionFixtureProvider
     */
    public function testDeleteThrowsCouldNotDeleteException()
    {
        $this->expectException(CouldNotDeleteException::class);

        $mockSmsSubscriptionResource = $this->createMock(SmsSubscriptionResource::class);

        $mockSmsSubscriptionResource->method('delete')->will($this->throwException(new \Exception));

        $smsSubscriptionEntity = $this->objectManager->create(SmsSubscriptionInterface::class);
        $smsSubscriptionRepository = $this->objectManager->create(
            SmsSubscriptionRepository::class,
            ['smsSubscriptionResourceModel' => $mockSmsSubscriptionResource]
        );

        $smsSubscriptionRepository->delete($smsSubscriptionEntity);
    }

    /**
     * @magentoDataFixture createSmsSubscriptionFixtureProvider
     */
    public function testDeleteByIdRemovesSmsSubscriptionEntity()
    {
        $result = $this->smsSubscriptionRepository->deleteById(self::$smsSubscriptionFixture->getId());

        $this->assertTrue($result);
    }

    public static function createSmsSubscriptionFixtureProvider()
    {
        self::$smsSubscriptionFixture = require __DIR__ . '/../_files/create_sms_subscription.php';
    }

    public static function createDisabledSubscriptionsFixtureProvider()
    {
        require __DIR__ . '/../_files/create_disabled_sms_subscriptions.php';
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->smsSubscriptionRepository = $this->objectManager->create(SmsSubscriptionRepository::class);
    }
}
