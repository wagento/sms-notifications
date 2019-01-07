<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Test\Integration\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace Linkmobility\Notifications\Test\Integration\Gateway;

use Linkmobility\Notifications\Gateway\ApiClient;
use Linkmobility\Notifications\Gateway\ApiException;
use Linkmobility\Notifications\Gateway\Entity\SuccessResultInterface;
use Linkmobility\Notifications\Gateway\Factory\ClientFactory;
use Linkmobility\Notifications\Gateway\Factory\ResultFactory;
use Linkmobility\Notifications\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * API Client Test
 *
 * @package Linkmobility\Notifications\Test\Integration\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ApiClientTest extends TestCase
{
    /**
     * @var \Linkmobility\Notifications\Gateway\ApiClient
     */
    private $apiClient;

    public function testSendRequestWithValidRequestReturnsSuccessResult()
    {
        /** @var \Linkmobility\Notifications\Model\Config $config */
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
