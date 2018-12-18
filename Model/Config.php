<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Model;

use Linkmobility\Notifications\Api\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Configuration Model
 *
 * @package Linkmobility\Notifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
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
}
