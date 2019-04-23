<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\Model;

use Wagento\SMSNotifications\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Configuration Model Test
 *
 * @package Wagento\SMSNotifications\Test\Integration\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ConfigTest extends TestCase
{
    /** @var \Wagento\SMSNotifications\Model\Config */
    private $config;

    /**
     * @magentoConfigFixture default/sms_notifications/general/enabled 1
     */
    public function testIsEnabled(): void
    {
        $this->assertTrue($this->config->isEnabled());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/general/require_optin 1
     */
    public function testIsOptinRequired(): void
    {
        $this->assertTrue($this->config->isOptinRequired());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/general/terms_and_conditions Test
     */
    public function testGetTermsAndConditions(): void
    {
        $this->assertEquals('Test', $this->config->getTermsAndConditions());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/general/show_terms_after_optin 1
     */
    public function testIsTermsAndConditionsShownAfterOptin(): void
    {
        $this->assertTrue($this->config->isTermsAndConditionsShownAfterOptin());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/general/send_welcome_message 1
     */
    public function testSendWelcomeMessage(): void
    {
        $this->assertTrue($this->config->sendWelcomeMessage());
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
     * @magentoConfigFixture default/sms_notifications/api/source_type MSISDN
     */
    public function testGetSourceType(): void
    {
        $this->assertEquals('MSISDN', $this->config->getSourceType());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/api/source +15555551234
     */
    public function testGetSource(): void
    {
        $this->assertEquals('+15555551234', $this->config->getSource());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/developer/debug 1
     */
    public function testIsLoggingEnabled(): void
    {
        $this->assertTrue($this->config->isLoggingEnabled());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/templates/welcome Test template
     */
    public function testGetWelcomeMessageTemplate(): void
    {
        $this->assertEquals('Test template', $this->config->getWelcomeMessageTemplate());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/templates/order_placed Test template
     */
    public function testGetOrderPlacedTemplate(): void
    {
        $this->assertEquals('Test template', $this->config->getOrderPlacedTemplate());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/templates/order_invoiced Test template
     */
    public function testGetOrderInvoicedTemplate(): void
    {
        $this->assertEquals('Test template', $this->config->getOrderInvoicedTemplate());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/templates/order_shipped Test template
     */
    public function testGetOrderShippedTemplate(): void
    {
        $this->assertEquals('Test template', $this->config->getOrderShippedTemplate());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/templates/order_refunded Test template
     */
    public function testGetOrderRefundedTemplate(): void
    {
        $this->assertEquals('Test template', $this->config->getOrderRefundedTemplate());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/templates/order_canceled Test template
     */
    public function testGetOrderCanceledTemplate(): void
    {
        $this->assertEquals('Test template', $this->config->getOrderCanceledTemplate());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/templates/order_held Test template
     */
    public function testGetOrderHeldTemplate(): void
    {
        $this->assertEquals('Test template', $this->config->getOrderHeldTemplate());
    }

    /**
     * @magentoConfigFixture default/sms_notifications/templates/order_released Test template
     */
    public function testGetOrderReleasedTemplate(): void
    {
        $this->assertEquals('Test template', $this->config->getOrderReleasedTemplate());
    }

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->config = $objectManager->create(Config::class);
    }

    protected function tearDown()
    {
        $this->config = null;
    }
}
