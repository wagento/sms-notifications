<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Test\Integration\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Test\Integration\Gateway;

use Wagento\LinkMobilitySMSNotifications\Gateway\ApiClient;
use Wagento\LinkMobilitySMSNotifications\Gateway\ApiException;
use Wagento\LinkMobilitySMSNotifications\Gateway\Entity\SuccessResultInterface;
use Wagento\LinkMobilitySMSNotifications\Gateway\Factory\ClientFactory;
use Wagento\LinkMobilitySMSNotifications\Gateway\Factory\ResultFactory;
use Wagento\LinkMobilitySMSNotifications\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * API Client Test
 *
 * @package Wagento\LinkMobilitySMSNotifications\Test\Integration\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ApiClientTest extends TestCase
{
    /**
     * @var \Wagento\LinkMobilitySMSNotifications\Gateway\ApiClient
     */
    private $apiClient;

    public function testSendRequestWithValidRequestReturnsSuccessResult(): void
    {
        /** @var \Wagento\LinkMobilitySMSNotifications\Model\Config $config */
        $config = Bootstrap::getObjectManager()->create(Config::class);

        $this->apiClient->setUri('send');
        $this->apiClient->setHttpMethod(ApiClient::HTTP_METHOD_POST);
        $this->apiClient->setUsername($config->getApiUser());
        $this->apiClient->setPassword($config->getApiPassword());
        $this->apiClient->setData([
            'platformId' => $config->getPlatformId(),
            'platformPartnerId' => $config->getPlatformPartnerId(),
            'sourceTON' => 'MSISDN',
            'source' => '+15555551234',
            'destination' => '+155555556789',
            'userData' => 'This is a test message!',
        ]);
        $this->apiClient->sendRequest();

        $this->assertInstanceOf(SuccessResultInterface::class, $this->apiClient->getResult());
    }

    public function testSendRequestWithInvalidRequestThrowsApiException(): void
    {
        $this->expectException(ApiException::class);

        $this->apiClient->setUri('send');
        $this->apiClient->setHttpMethod(ApiClient::HTTP_METHOD_POST);
        $this->apiClient->setUsername('invalid');
        $this->apiClient->setPassword('invalid');
        $this->apiClient->setData([
            'platformId' => 'invalid',
            'platformPartnerId' => 'invalid',
            'sourceTON' => 'MSISDN',
            'source' => '+15555551234',
            'destination' => '+155555556789',
            'userData' => 'This is a test message!',
        ]);
        $this->apiClient->sendRequest();
    }

    protected function setUp()
    {
        parent::setUp();

        $this->apiClient = new ApiClient(new ClientFactory(), new ResultFactory());
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->apiClient = null;
    }
}
