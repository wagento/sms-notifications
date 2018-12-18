<?php
/**
 * Link Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Model\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Model\Data;

use Linkmobility\Notifications\Api\Data\SmsTypeInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * SMS Type Entity
 *
 * @package Linkmobility\Notifications\Mdoel\Data
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class SmsType extends AbstractSimpleObject implements SmsTypeInterface
{
    /**
     * @param int $smsTypeId
     * @return \Linkmobility\Notifications\Api\Data\SmsTypeInterface
     */
    public function setId(int $smsTypeId): SmsTypeInterface
    {
        return $this->setSmsTypeId($smsTypeId);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->getSmsTypeId();
    }

    /**
     * @param int $smsTypeId
     * @return \Linkmobility\Notifications\Api\Data\SmsTypeInterface
     */
    public function setSmsTypeId(int $smsTypeId): SmsTypeInterface
    {
        return $this->setData(self::SMS_TYPE_ID, $smsTypeId);
    }

    /**
     * @return int|null
     */
    public function getSmsTypeId(): ?int
    {
        return $this->_get(self::SMS_TYPE_ID);
    }

    /**
     * @param string $name
     * @return \Linkmobility\Notifications\Api\Data\SmsTypeInterface
     */
    public function setName(string $name): SmsTypeInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->_get(self::NAME);
    }

    /**
     * @param bool $isActive
     * @return \Linkmobility\Notifications\Api\Data\SmsTypeInterface
     */
    public function setIsActive(bool $isActive): SmsTypeInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return (bool)$this->_get(self::IS_ACTIVE);
    }

    /**
     * @param string $description
     * @return \Linkmobility\Notifications\Api\Data\SmsTypeInterface
     */
    public function setDescription(string $description): SmsTypeInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->_get(self::DESCRIPTION);
    }
}
