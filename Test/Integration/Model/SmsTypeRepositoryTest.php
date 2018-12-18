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

use Linkmobility\Notifications\Api\Data\SmsTypeInterface;
use LinkMobility\Notifications\Model\ResourceModel\SmsType as SmsTypeResource;
use Linkmobility\Notifications\Model\SmsTypeFactory as SmsTypeModelFactory;
use Linkmobility\Notifications\Model\SmsTypeRepository;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * SMS Type Repository Test
 *
 * @package Linkmobility\Notifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsTypeRepositoryTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;
    /**
     * @var \Linkmobility\Notifications\Model\SmsTypeRepository
     */
    private $smsTypeRepository;
    /**
     * @var \Linkmobility\Notifications\Model\SmsType
     */
    private static $smsTypeFixture;

    /**
     * @magentoDataFixture createSmsTypeFixtureProvider
     */
    public function testGetReturnSmsTypeEntity()
    {
        $result = $this->smsTypeRepository->get(self::$smsTypeFixture->getId());

        $this->assertInstanceOf(SmsTypeInterface::class, $result);
        $this->assertEquals(self::$smsTypeFixture->getDataModel()->__toArray(), $result->__toArray());
    }

    public function testGetThrowsNoSuchEntityException()
    {
        $this->expectException(NoSuchEntityException::class);

        $this->smsTypeRepository->get(1000);
    }

    /**
     * @magentoDataFixture createDisabledTypesFixtureProvider
     */
    public function testGetListReturnsSearchResults()
    {
        $searchCriteria = $this->objectManager->create(SearchCriteriaBuilderFactory::class)
            ->create()
            ->addFilter('is_active', 0)
            ->create();
        $results = $this->smsTypeRepository->getList($searchCriteria);

        $this->assertInstanceOf(SearchResultsInterface::class, $results);
        $this->assertEquals(10, $results->getTotalCount());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveCreatesSmsTypeEntity()
    {
        $smsTypeEntity = $this->objectManager->create(SmsTypeInterface::class)
            ->setName('Test')
            ->setDescription('Test notification type')
            ->setIsActive(true);

        $result = $this->smsTypeRepository->save($smsTypeEntity);

        $this->assertInstanceOf(SmsTypeInterface::class, $result);
    }

    /**
     * @magentoDataFixture createSmsTypeFixtureProvider
     */
    public function testSaveUpdatesSmsTypeEntity()
    {
        $smsTypeEntity = $this->objectManager->create(SmsTypeInterface::class)
            ->setSmsTypeId(self::$smsTypeFixture->getId())
            ->setIsActive(false);

        $result = $this->smsTypeRepository->save($smsTypeEntity);

        $this->assertFalse($result->getIsActive());
    }

    /**
     * @magentoDataFixture createSmsTypeFixtureProvider
     */
    public function testDeleteRemovesEntity()
    {
        $smsTypeResource = $this->objectManager->create(SmsTypeResource::class);
        /** @var \Linkmobility\Notifications\Model\SmsType $smsTypeModel */
        $smsTypeModel = $this->objectManager->create(SmsTypeModelFactory::class)->create();

        $smsTypeResource->load($smsTypeModel, self::$smsTypeFixture->getId());

        $result = $this->smsTypeRepository->delete($smsTypeModel->getDataModel());

        $this->assertTrue($result);
    }

    /**
     * @magentoDataFixture createSmsTypeFixtureProvider
     */
    public function testDeleteThrowsCouldNotDeleteException()
    {
        $this->expectException(CouldNotDeleteException::class);

        $mockSmsTypeResource = $this->createMock(SmsTypeResource::class);

        $mockSmsTypeResource->method('delete')->will($this->throwException(new \Exception));

        $smsTypeEntity = $this->objectManager->create(SmsTypeInterface::class);
        $smsTypeRepository = $this->objectManager->create(
            SmsTypeRepository::class,
            ['smsTypeResourceModel' => $mockSmsTypeResource]
        );

        $smsTypeRepository->delete($smsTypeEntity);
    }

    /**
     * @magentoDataFixture createSmsTypeFixtureProvider
     */
    public function testDeleteByIdRemovesEntity()
    {
        $result = $this->smsTypeRepository->deleteById(self::$smsTypeFixture->getId());

        $this->assertTrue($result);
    }

    public static function createSmsTypeFixtureProvider()
    {
        self::$smsTypeFixture = require __DIR__ . '/../_files/create_sms_type.php';
    }

    public static function createDisabledTypesFixtureProvider()
    {
        require __DIR__ . '/../_files/create_disabled_types.php';
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->smsTypeRepository = $this->objectManager->create(SmsTypeRepository::class);
    }
}
