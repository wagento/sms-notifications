<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Model\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Model\Data;

use Wagento\SMSNotifications\Api\Data\TelephonePrefixInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Telephone Prefix Entity
 *
 * @package Wagento\SMSNotifications\Model\Data
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Magento2.PHP.FinalImplementation.FoundFinal -- This entity is not meant to be extended. Inherit from
 * its interface instead.
 */
final class TelephonePrefix extends AbstractSimpleObject implements TelephonePrefixInterface
{
    public function setCountryCode(string $countryCode): TelephonePrefixInterface
    {
        return $this->setData(self::COUNTRY_CODE, $countryCode);
    }

    public function getCountryCode(): string
    {
        return (string)$this->_get(self::COUNTRY_CODE);
    }

    public function setCountryName(string $countryName): TelephonePrefixInterface
    {
        return $this->setData(self::COUNTRY_NAME, $countryName);
    }

    public function getCountryName(): string
    {
        return (string)$this->_get(self::COUNTRY_NAME);
    }

    public function setPrefix(int $prefix): TelephonePrefixInterface
    {
        return $this->setData(self::PREFIX, $prefix);
    }

    public function getPrefix(): int
    {
        return (int)$this->_get(self::PREFIX);
    }
}
