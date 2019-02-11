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
use Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * SMS Sender
 *
 * @package Linkmobility\Notifications\Model
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
     * @var \Linkmobility\Notifications\Api\ConfigInterface
     */
    protected $config;
    /**
     * @var \Linkmobility\Notifications\Model\MessageService
     */
    protected $messageService;
    /**
     * @var \Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface
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

    /**
     * @param string|int $storeId
     * @return string|int|null
     */
    protected function getWebsiteIdByStoreId($storeId)
    {
        try {
            $websiteId = $this->storeRepository->getById($storeId)->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        return $websiteId;
    }

    /**
     * @param string|int|null $websiteId
     */
    protected function isModuleEnabled($websiteId): bool
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
