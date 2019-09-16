<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Plugin\Customer\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Plugin\Customer\Api;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Wagento\SMSNotifications\Api\ConfigInterface;
use Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface;
use Wagento\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;

/**
 * Plugin for {@see \Magento\Customer\Api\CustomerRepositoryInterface}
 *
 * @package Wagento\SMSNotifications\Plugin\Customer\Api
 * @author Joseph Leedy <joseph@wagento.com>
 */
class CustomerRepositoryInterfacePlugin
{
    /**
     * @var \Wagento\SMSNotifications\Api\ConfigInterface
     */
    private $config;
    /**
     * @var \Wagento\SMSNotifications\Api\SmsSubscriptionRepositoryInterface
     */
    private $smsSubscriptionRepository;

    public function __construct(ConfigInterface $config, SmsSubscriptionRepositoryInterface $smsSubscriptionRepository)
    {
        $this->config = $config;
        $this->smsSubscriptionRepository = $smsSubscriptionRepository;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        CustomerRepositoryInterface $subject,
        CustomerInterface $result,
        string $email,
        ?int $websiteId = null
    ): CustomerInterface {
        if ($this->smsNotificationsIsEnabled($websiteId)) {
            $this->attachSmsSubscriptionsToCustomer($result);
        }

        return $result;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetById(
        CustomerRepositoryInterface $subject,
        CustomerInterface $result,
        int $customerId
    ): CustomerInterface {
        if ($this->smsNotificationsIsEnabled()) {
            $this->attachSmsSubscriptionsToCustomer($result);
        }

        return $result;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        CustomerRepositoryInterface $subject,
        SearchResultsInterface $result,
        SearchCriteriaInterface $searchCriteria
    ): SearchResultsInterface {
        if ($this->smsNotificationsIsEnabled()) {
            array_map(function (CustomerInterface $customer) {
                $this->attachSmsSubscriptionsToCustomer($customer);
            }, $result->getItems());
        }

        return $result;
    }

    private function smsNotificationsIsEnabled(?int $websiteId = null): bool
    {
        return $this->config->isEnabled($websiteId);
    }

    private function attachSmsSubscriptionsToCustomer(CustomerInterface $customer): void
    {
        $smsSubscriptions = $this->smsSubscriptionRepository->getListByCustomerId((int)$customer->getId());

        if ($smsSubscriptions->getTotalCount() > 0) {
            $customer->getExtensionAttributes()->setSmsNotificationSubscriptions(
                array_column($smsSubscriptions->getItems(), SmsSubscriptionInterface::SMS_TYPE)
            );
        }
    }
}
