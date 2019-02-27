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
use LinkMobility\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * SMS Sender
 *
 * @package LinkMobility\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
abstract class SmsSender
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var \LinkMobility\SMSNotifications\Api\ConfigInterface
     */
    protected $config;
    /**
     * @var \LinkMobility\SMSNotifications\Model\MessageService
     */
    protected $messageService;
    /**
     * @var \LinkMobility\SMSNotifications\Api\SmsSubscriptionRepositoryInterface
     */
    private $subscriptionRepository;

    public function __construct(
        LoggerInterface $logger,
        StoreRepositoryInterface $storeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository,
        ConfigInterface $config,
        SmsSubscriptionRepositoryInterface $subscriptionRepository,
        MessageService $messageService
    ) {
        $this->logger = $logger;
        $this->storeRepository = $storeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRepository = $customerRepository;
        $this->config = $config;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->messageService = $messageService;
    }

    abstract public function send(AbstractModel $entity): bool;

    /**
     * @param string|int $storeId
     */
    protected function getWebsiteIdByStoreId($storeId): ?int
    {
        try {
            $websiteId = (int)$this->storeRepository->getById($storeId)->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        return $websiteId;
    }

    protected function isModuleEnabled(?int $websiteId = null): bool
    {
        return $this->config->isEnabled($websiteId);
    }

    protected function getCustomerMobilePhoneNumber(int $customerId): ?string
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException | LocalizedException $e) {
            $this->logger->critical(
                __('Could not get mobile telephone number for customer. Error: %1', $e->getMessage()),
                [
                    'customer_id' => $customerId
                ]
            );

            return null;
        }

        $mobilePhonePrefixAttribute = $customer->getCustomAttribute('sms_mobile_phone_prefix');
        $mobilePhoneNumberAttribute = $customer->getCustomAttribute('sms_mobile_phone_number');

        if (
            $mobilePhonePrefixAttribute === null
            || $mobilePhoneNumberAttribute === null
            || empty($mobilePhonePrefixAttribute->getValue())
            || empty($mobilePhoneNumberAttribute->getValue())
        ) {
            return null;
        }

        $mobilePhonePrefix = '+' . substr($mobilePhonePrefixAttribute->getValue(), 3);
        $mobilePhoneNumber = preg_replace('/\D+/', '', $mobilePhoneNumberAttribute->getValue());

        return $mobilePhonePrefix . $mobilePhoneNumber;
    }

    protected function getCustomerSmsSubscriptions(int $customerId): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customerId)->create();
        $searchResults = $this->subscriptionRepository->getList($searchCriteria);

        if ($searchResults->getTotalCount() === 0) {
            return [];
        }

        return array_column($searchResults->getItems(), 'sms_type');
    }
}
