<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Test\Integration\Gateway;

use LinkMobility\SMSNotifications\Gateway\ApiClient;
use LinkMobility\SMSNotifications\Gateway\ApiException;
use LinkMobility\SMSNotifications\Gateway\Entity\SuccessResultInterface;
use LinkMobility\SMSNotifications\Gateway\Factory\ClientFactory;
use LinkMobility\SMSNotifications\Gateway\Factory\ResultFactory;
use LinkMobility\SMSNotifications\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * API Client Test
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ApiClientTest extends TestCase
{
    /**
     * @var \LinkMobility\SMSNotifications\Gateway\ApiClient
     */
    private $apiClient;

    public function testSendRequestWithValidRequestReturnsSuccessResult()
    {
        /** @var \LinkMobility\SMSNotifications\Model\Config $config */
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

    public function testSendRequestWithInvaliRequestThrowsApiException()
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
}
