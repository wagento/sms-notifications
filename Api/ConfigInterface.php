<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Api;

use Magento\Store\Model\ScopeInterface;

/**
 * Configuration Model Interface
 *
 * @package LinkMobility\SMSNotifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */
interface ConfigInterface
{
    const XML_PATH_ENABLED = 'sms_notifications/general/enabled';
    const XML_PATH_REQUIRE_OPTIN = 'sms_notifications/general/require_optin';
    const XML_PATH_TERMS_AND_CONDITIONS = 'sms_notifications/general/terms_and_conditions';
    const XML_PATH_SHOW_TERMS_AFTER_OPTIN = 'sms_notifications/general/show_terms_after_optin';
    const XML_PATH_API_USER = 'sms_notifications/api/username';
    const XML_PATH_API_PASSWORD = 'sms_notifications/api/password';
    const XML_PATH_PLATFORM_ID = 'sms_notifications/api/platform_id';
    const XML_PATH_PLATFORM_PARTNER_ID = 'sms_notifications/api/platform_partner_id';
    const XML_PATH_GATE_ID = 'sms_notifications/api/gate_id';
    const XML_PATH_SOURCE_TYPE = 'sms_notifications/api/source_type';
    const XML_PATH_SOURCE = 'sms_notifications/api/source';
    const XML_PATH_ENABLE_LOGGING = 'sms_notifications/developer/debug';
    const XML_PATH_TEMPLATE_ORDER_PLACED = 'sms_notifications/templates/order_placed';
    const XML_PATH_TEMPLATE_ORDER_UPDATED = 'sms_notifications/templates/order_updated';
    const XML_PATH_TEMPLATE_ORDER_SHIPPED = 'sms_notifications/templates/order_shipped';
    const XML_PATH_TEMPLATE_ORDER_REFUNDED = 'sms_notifications/templates/order_refunded';
    const XML_PATH_TEMPLATE_ORDER_CANCELED = 'sms_notifications/templates/order_canceled';
    const XML_PATH_TEMPLATE_ORDER_HELD = 'sms_notifications/templates/order_held';
    const XML_PATH_TEMPLATE_ORDER_RELEASED = 'sms_notifications/templates/order_released';

    public function isEnabled(?int $websiteId = null): bool;

    public function isOptinRequired(?int $websiteId = null): bool;

    public function getTermsAndConditions(?int $websiteId = null): ?string;

    public function isTermsAndConditionsShownAfterOptin(?int $websiteId = null): bool;

    public function getApiUser(?int $websiteId = null): ?string;

    public function getApiPassword(?int $websiteId = null): ?string;

    public function getPlatformId(?int $websiteId = null): ?string;

    public function getPlatformPartnerId(?int $websiteId = null): ?string;

    public function getGateId(?int $websiteId = null): ?string;

    public function getSourceType(?int $websiteId = null): ?string;

    public function getSource(?int $websiteId = null): ?string;

    public function isLoggingEnabled(): bool;

    public function getOrderPlacedTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string;

    public function getOrderUpdatedTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string;

    public function getOrderShippedTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string;

    public function getOrderRefundedTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string;

    public function getOrderCanceledTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string;

    public function getOrderHeldTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string;

    public function getOrderReleasedTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string;
}
