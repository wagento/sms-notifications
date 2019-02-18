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

use LinkMobility\SMSNotifications\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Configuration Model Test
 *
 * @package LinkMobility\SMSNotifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ConfigTest extends TestCase
{
    /** @var \LinkMobility\SMSNotifications\Model\Config */
    private $config;

    /**
     * @magentoConfigFixture default/sms_notifications/general/enabled 1
     */
    public function testIsEnabled(): void
    {
        $this->assertTrue($this->config->isEnabled());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/api/username LINKMOBILITY
     */
    public function testGetApiUser(): void
    {
        $this->assertEquals('LINKMOBILITY', $this->config->getApiUser());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/api/password P@55w0rd
     */
    public function testGetApiPassword(): void
    {
        $this->assertEquals('P@55w0rd', $this->config->getApiPassword());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/api/platform_id ABCDE
     */
    public function testGetPlatformId(): void
    {
        $this->assertEquals('ABCDE', $this->config->getPlatformId());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/api/platform_partner_id 123
     */
    public function testGetPlatformPartnerId(): void
    {
        $this->assertEquals('123', $this->config->getPlatformPartnerId());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/api/gate_id ABCD123
     */
    public function testGetGateId(): void
    {
        $this->assertEquals('ABCD123', $this->config->getGateId());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/api/source_number +15555551234
     */
    public function testGetSourceNumber(): void
    {
        $this->assertEquals('+15555551234', $this->config->getSourceNumber());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/developer/debug 1
     */
    public function testIsLoggingEnabled(): void
    {
        $this->assertTrue($this->config->isLoggingEnabled());
    }

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->config = $objectManager->create(Config::class);
    }
}
