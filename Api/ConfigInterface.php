<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Api;

/**
 * Configuration Model Interface
 *
 * @package Linkmobility\Notifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
interface ConfigInterface
{
    const XML_PATH_ENABLED = 'sms_notifications/general/enabled';
    const XML_PATH_API_USER = 'sms_notifications/api/username';
    const XML_PATH_API_PASSWORD = 'sms_notifications/api/password';
    const XML_PATH_PLATFORM_ID = 'sms_notifications/api/platform_id';
    const XML_PATH_PLATFORM_PARTNER_ID = 'sms_notifications/api/platform_partner_id';
    const XML_PATH_GATE_ID = 'sms_notifications/api/gate_id';
    const XML_PATH_SOURCE_NUMBER = 'sms_notifications/api/source_number';
    const XML_PATH_ENABLE_LOGGING = 'sms_notifications/developer/debug';
    const XML_PATH_ORDER_SUCCESSFUL = 'sms_notifications/order_templates/successful';
    const XML_PATH_ORDER_UPDATED = 'sms_notifications/order_templates/updated';
    const XML_PATH_ORDER_SHIPPED = 'sms_notifications/order_templates/shipped';
    const XML_PATH_ORDER_REFUNDED = 'sms_notifications/order_templates/refunded';
    const XML_PATH_ORDER_CANCELED = 'sms_notifications/order_templates/canceled';


    public function isEnabled(string $websiteId = null): bool;

    public function getApiUser(string $websiteId = null): ?string;

    public function getApiPassword(string $websiteId = null): ?string;

    public function getPlatformId(string $websiteId = null): ?string;

    public function getPlatformPartnerId(string $websiteId = null): ?string;

    public function getGateId(string $websiteId = null): ?string;

    public function getSourceNumber(string $websiteId = null): ?string;

    public function isLoggingEnabled(): bool;

    public function getOrderSuccessfulTpl(string $websiteId = null) : ?string;

    public function getOrderUpdatedTpl(string $websiteId = null) : ?string;

    public function getOrderShippedTpl(string $websiteId = null) : ?string;

    public function getOrderRefundedTpl(string $websiteId = null) : ?string;

    public function getOrderCanceledTpl(string $websiteId = null) : ?string;

}
