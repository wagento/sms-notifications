<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model;

use LinkMobility\SMSNotifications\Api\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Configuration Model
 *
 * @package LinkMobility\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */
final class Config implements ConfigInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(string $websiteId = null): bool
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, $scope, $websiteId);
    }

    public function getTermsAndConditions(string $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_TERMS_AND_CONDITIONS, $scope, $websiteId);
    }

    public function getApiUser(string $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_API_USER, $scope, $websiteId);
    }

    public function getApiPassword(string $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_API_PASSWORD, $scope, $websiteId);
    }

    public function getPlatformId(string $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_PLATFORM_ID, $scope, $websiteId);
    }

    public function getPlatformPartnerId(string $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_PLATFORM_PARTNER_ID, $scope, $websiteId);
    }

    public function getGateId(string $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_GATE_ID, $scope, $websiteId);
    }

    public function getSourceNumber(string $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_SOURCE_NUMBER, $scope, $websiteId);
    }

    public function isLoggingEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_LOGGING);
    }

    public function getOrderPlacedTemplate(string $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_PLACED, $scopeType, $scopeId);
    }

    public function getOrderUpdatedTemplate(string $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_UPDATED, $scopeType, $scopeId);
    }

    public function getOrderShippedTemplate(string $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_SHIPPED, $scopeType, $scopeId);
    }

    public function getOrderRefundedTemplate(string $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_REFUNDED, $scopeType, $scopeId);
    }

    public function getOrderCanceledTemplate(string $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_CANCELED, $scopeType, $scopeId);
    }

    public function getOrderHeldTemplate(string $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_HELD, $scopeType, $scopeId);
    }

    public function getOrderReleasedTemplate(string $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_RELEASED, $scopeType, $scopeId);
    }
}
