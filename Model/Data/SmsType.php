<?php
/**
 * Link Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Model\Data
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <@wagento.com>
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
    public function setId(int $smsTypeId): SmsTypeInterface
    {
        return $this->setSmsTypeId($smsTypeId);
    }

    public function getId(): ?int
    {
        return $this->getSmsTypeId();
    }

    public function setSmsTypeId(int $smsTypeId): SmsTypeInterface
    {
        return $this->setData(self::SMS_TYPE_ID, $smsTypeId);
    }

    public function getSmsTypeId(): ?int
    {
        return $this->_get(self::SMS_TYPE_ID);
    }

    public function setName(string $name): SmsTypeInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getName(): ?string
    {
        return $this->_get(self::NAME);
    }

    public function setStatus(string $status): SmsTypeInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getStatus(): ?string
    {
        return $this->_get(self::STATUS);
    }

    public function setDescription(string $description): SmsTypeInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getDescription(): ?string
    {
        return $this->_get(self::DESCRIPTION);
    }
}
