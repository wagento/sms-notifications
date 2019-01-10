<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Gateway\Entity;

/**
 * Error Result Entity
 *
 * @package Linkmobility\Notifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class ErrorResult implements ErrorResultInterface
{
    private const TYPE = 'error';

    private $status = 0;
    private $description = '';
    private $translatedDescription;

    public function __construct(array $data = [])
    {
        if (array_key_exists('status', $data)) {
            $this->setStatus($data['status']);
        }

        if (array_key_exists('description', $data)) {
            $this->setDescription($data['description']);
        }

        if (array_key_exists('translatedDescription', $data)) {
            $this->setTranslatedDescription($data['translatedDescription']);
        }
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setTranslatedDescription(?string $translatedDescription): void
    {
        $this->translatedDescription = $translatedDescription;
    }

    public function getTranslatedDescription(): ?string
    {
        return $this->translatedDescription;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getCode(): int
    {
        return $this->getStatus();
    }

    public function getMessage(): string
    {
        return $this->getDescription();
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'description' => $this->description,
            'translatedDescription' => $this->translatedDescription
        ];
    }
}
