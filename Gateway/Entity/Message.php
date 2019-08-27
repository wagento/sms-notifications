<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Gateway\Entity;

/**
 * Message Entity
 *
 * @package Wagento\SMSNotifications\Gateway\Entity
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @phpcs:disable Magento2.PHP.FinalImplementation.FoundFinal -- This class is not meant to be extended. Inherit from
 * its interface instead.
 */
final class Message implements MessageInterface
{
    /**
     * Phone number from where the message should originate
     *
     * @var string
     */
    private $source;
    /**
     * Type of source phone number
     *
     * @var \Wagento\SMSNotifications\Gateway\Entity\TON
     */
    private $sourceTON;
    /**
     * Phone number where the message should be sent
     *
     * @var string
     */
    private $destination;
    /**
     * Type of destination phone number
     *
     * @var \Wagento\SMSNotifications\Gateway\Entity\TON
     */
    private $destinationTON;
    /**
     * Data Coding Scheme to use when sending messages
     *
     * @var \Wagento\SMSNotifications\Gateway\Entity\DCS
     */
    private $dcs;
    /**
     * 8-bit hex encoded value that may be specified when sending concatenated
     * SMS, WAP-push, etc.
     *
     * @var string
     */
    private $userDataHeader;
    /**
     * Message to be sent. Note: must be split if over 140 bytes (160 chars)
     *
     * @var string
     */
    private $userData;
    /**
     * Whether a report should be returned after the message is sent
     *
     * @var bool
     */
    private $useDeliveryReport;
    /**
     * One or more gates that should be used for sending delivery reports
     *
     * @var string[]
     */
    private $deliveryReportGates;
    /**
     * How long the message is supposed to live, in milliseconds (default
     * 172800000/48 hours). If a message takes longer to send, it will be
     * discarded.
     *
     * @var float
     */
    private $relativeValidityTime;
    /**
     * Absolute time when a message should expire between 15 minutes and 48
     * hours, in RFC3339 format. Overrides $relativeValidityTime.
     *
     * @var \DateTimeInterface
     */
    private $absoluteValidityTime;
    /**
     * Price, in local currency, in 1/100 of currency
     *
     * @var int
     */
    private $tariff;
    /**
     * Currency to use if not using default country currency. Supported
     * currencies are NOK, SEK, DKK, EUR, LTL.
     *
     * @var string
     */
    private $currency;
    /**
     * Value Added Tax used for the transaction, in 1/100 of currency
     *
     * @var int
     */
    private $vat;
    /**
     * Minimum allowed age for message content
     *
     * @var int
     */
    private $age;
    /**
     * Platform ID provided by Support
     *
     * @var string
     */
    private $platformId;
    /**
     * Platform Partner ID provided by Support
     *
     * @var string
     */
    private $platformPartnerId;
    /**
     * Internal reference ID, not used by service
     *
     * @var string
     */
    private $refId;
    /**
     * Service description to display on user's phone bill (for premium message)
     *
     * @var string
     */
    private $productDescription;
    /**
     * Service category for premium message
     *
     * @var int
     */
    private $productCategory;
    /**
     * Reference to the originator of the message
     *
     * @var string
     */
    private $moReferenceId;
    /**
     * Extra settings to send with the message
     *
     * @var array
     */
    private $customParameters;
    /**
     * Whether the message API should return a response or empty body
     *
     * @var bool
     */
    private $ignoreResponse;

    public function __construct(
        string $source = null,
        string $sourceTON = null,
        string $destination = null,
        string $userData = null,
        string $platformId = null,
        string $platformPartnerId = null
    ) {
        $this->setSourceTON($sourceTON);

        $this->source = $source;
        $this->destination = $destination;
        $this->userData = $userData;
        $this->platformId = $platformId;
        $this->platformPartnerId = $platformPartnerId;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param \Wagento\SMSNotifications\Gateway\Entity\TON|string $sourceTON
     * @return void
     */
    public function setSourceTON($sourceTON): void
    {
        if ($sourceTON !== null && !($sourceTON instanceof TON)) {
            $sourceTON = new TON($sourceTON);
        }

        $this->sourceTON = $sourceTON;
    }

    public function getSourceTON(): TON
    {
        return $this->sourceTON;
    }

    public function setDestination(string $destination): void
    {
        $this->destination = $destination;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * @param \Wagento\SMSNotifications\Gateway\Entity\TON|string $destinationTON
     */
    public function setDestinationTON($destinationTON): void
    {
        if ($destinationTON !== null && !($destinationTON instanceof TON)) {
            $destinationTON = new TON($destinationTON);
        }

        $this->destinationTON = $destinationTON;
    }

    public function getDestinationTON(): TON
    {
        return $this->destinationTON;
    }

    /**
     * @param \Wagento\SMSNotifications\Gateway\Entity\DCS|string $dcs
     */
    public function setDcs($dcs): void
    {
        if ($dcs !== null && !($dcs instanceof DCS)) {
            $dcs = new DCS($dcs);
        }

        $this->dcs = $dcs;
    }

    public function getDcs(): DCS
    {
        return $this->dcs;
    }

    public function setUserDataHeader(string $userDataHeader): void
    {
        $this->userDataHeader = $userDataHeader;
    }

    public function getUserDataHeader(): string
    {
        return $this->userDataHeader;
    }

    public function setUserData(string $userData): void
    {
        $this->userData = $userData;
    }

    public function getUserData(): string
    {
        return $this->userData;
    }

    public function setUseDeliveryReport(bool $useDeliveryReport): void
    {
        $this->useDeliveryReport = $useDeliveryReport;
    }

    public function getUseDeliveryReport(): bool
    {
        return $this->useDeliveryReport;
    }

    /**
     * @param string[] $deliveryReportGates
     */
    public function setDeliveryReportGates(array $deliveryReportGates): void
    {
        $this->deliveryReportGates = $deliveryReportGates;
    }

    /**
     * @return string[]
     */
    public function getDeliveryReportGates(): array
    {
        return $this->deliveryReportGates;
    }

    public function setRelativeValidityTime(float $relativeValidityTime): void
    {
        $this->relativeValidityTime = $relativeValidityTime;
    }

    public function getRelativeValidityTime(): float
    {
        return $this->relativeValidityTime;
    }

    /**
     * @param \DateTimeInterface|string $absoluteValidityTime
     * @throws \Exception
     */
    public function setAbsoluteValidityTime($absoluteValidityTime): void
    {
        if (!($absoluteValidityTime instanceof \DateTimeInterface)) {
            $absoluteValidityTime = new \DateTime($absoluteValidityTime);
        }

        $this->absoluteValidityTime = $absoluteValidityTime;
    }

    public function getAbsoluteValidityTime(): \DateTimeInterface
    {
        return $this->absoluteValidityTime;
    }

    public function setTariff(int $tariff): void
    {
        $this->tariff = $tariff;
    }

    public function getTariff(): int
    {
        return $this->tariff;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setVat(int $vat): void
    {
        $this->vat = $vat;
    }

    public function getVat(): int
    {
        return $this->vat;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setPlatformId(string $platformId): void
    {
        $this->platformId = $platformId;
    }

    public function getPlatformId(): string
    {
        return $this->platformId;
    }

    public function setPlatformPartnerId(string $platformPartnerId): void
    {
        $this->platformPartnerId = $platformPartnerId;
    }

    public function getPlatformPartnerId(): string
    {
        return $this->platformPartnerId;
    }

    public function setRefId(string $refId): void
    {
        $this->refId = $refId;
    }

    public function getRefId(): string
    {
        return $this->refId;
    }

    public function setProductDescription(string $productDescription): void
    {
        $this->productDescription = $productDescription;
    }

    public function getProductDescription(): string
    {
        return $this->productDescription;
    }

    public function setProductCategory(int $productCategory): void
    {
        $this->productCategory = $productCategory;
    }

    public function getProductCategory(): int
    {
        return $this->productCategory;
    }

    public function setMoReferenceId(string $moReferenceId): void
    {
        $this->moReferenceId = $moReferenceId;
    }

    public function getMoReferenceId(): string
    {
        return $this->moReferenceId;
    }

    public function setCustomParameters(array $customParameters): void
    {
        $this->customParameters = $customParameters;
    }

    public function getCustomParameters(): array
    {
        return $this->customParameters;
    }

    public function setIgnoreResponse(bool $ignoreResponse): void
    {
        $this->ignoreResponse = $ignoreResponse;
    }

    public function getIgnoreResponse(): bool
    {
        return $this->ignoreResponse;
    }
}
