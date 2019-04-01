<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Gateway\Entity;

/**
 * Message Entity Interface
 *
 * @package LinkMobility\SMSNotifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface MessageInterface
{
    public function setSource(string $source): void;

    public function getSource(): string;

    /**
     * @param \LinkMobility\SMSNotifications\Gateway\Entity\TON|string $sourceTon
     * @return void
     */
    public function setSourceTON($sourceTon): void;

    public function getSourceTON(): TON;

    public function setDestination(string $destination): void;

    public function getDestination(): string;

    /**
     * @param \LinkMobility\SMSNotifications\Gateway\Entity\TON|string $destinationTon
     */
    public function setDestinationTON($destinationTon): void;

    public function getDestinationTON(): TON;

    /**
     * @param \LinkMobility\SMSNotifications\Gateway\Entity\DCS|string $dcs
     */
    public function setDcs($dcs): void;

    public function getDcs(): DCS;

    public function setUserDataHeader(string $userDataHeader): void;

    public function getUserDataHeader(): string;

    public function setUserData(string $userData): void;

    public function getUserData(): string;

    public function setUseDeliveryReport(bool $useDeliveryReport): void;

    public function getUseDeliveryReport(): bool;

    /**
     * @param string[] $deliveryReportGates
     */
    public function setDeliveryReportGates(array $deliveryReportGates): void;

    /**
     * @return string[]
     */
    public function getDeliveryReportGates(): array;

    public function setRelativeValidityTime(float $relativeValidityTime): void;

    public function getRelativeValidityTime(): float;

    /**
     * @param \DateTimeInterface|string $absoluteValidityTime
     * @throws \Exception
     */
    public function setAbsoluteValidityTime($absoluteValidityTime): void;

    public function getAbsoluteValidityTime(): \DateTimeInterface;

    public function setTariff(int $tariff): void;

    public function getTariff(): int;

    public function setCurrency(string $currency): void;

    public function getCurrency(): string;

    public function setVat(int $vat): void;

    public function getVat(): int;

    public function setAge(int $age): void;

    public function getAge(): int;

    public function setPlatformId(string $platformId): void;

    public function getPlatformId(): string;

    public function setPlatformPartnerId(string $platformPartnerId): void;

    public function getPlatformPartnerId(): string;

    public function setRefId(string $refId): void;

    public function getRefId(): string;

    public function setProductDescription(string $productDescription): void;

    public function getProductDescription(): string;

    public function setProductCategory(int $productCategory): void;

    public function getProductCategory(): int;

    public function setMoReferenceId(string $moReferenceId): void;

    public function getMoReferenceId(): string;

    public function setCustomParameters(array $customParameters): void;

    public function getCustomParameters(): array;

    public function setIgnoreResponse(bool $ignoreResponse): void;

    public function getIgnoreResponse(): bool;
}
