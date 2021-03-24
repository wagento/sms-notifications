<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\ViewModel
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\ViewModel;

use Wagento\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Customer SMS Subscriptions View Model
 *
 * @package Wagento\SMSNotifications\ViewModel
 * @author Joseph Leedy <joseph@wagento.com>
 */
class CustomerSmsSubscriptions implements ArgumentInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Wagento\SMSNotifications\Api\SmsSubscriptionRepositoryInterface
     */
    private $smsSubscriptionRepository;
    /**
     * @var string[]
     */
    private $subscribedSmsTypes;

    public function __construct(
        CustomerSession $customerSession,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SmsSubscriptionRepositoryInterface $smsSubscriptionRepository
    ) {
        $this->customerSession = $customerSession;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->smsSubscriptionRepository = $smsSubscriptionRepository;
    }

    /**
     * @param string|int|null $customerId
     */
    public function getSubscribedSmsTypes($customerId = null): array
    {
        if ($this->subscribedSmsTypes !== null) {
            return $this->subscribedSmsTypes;
        }

        $this->subscribedSmsTypes = [];

        if ($customerId === null) {
            $customerId = $this->customerSession->getCustomerId();
        }

        if ($customerId === null) {
            return $this->subscribedSmsTypes;
        }

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customerId)->create();
        $searchResults = $this->smsSubscriptionRepository->getList($searchCriteria);

        if ($searchResults->getTotalCount() === 0) {
            return $this->subscribedSmsTypes;
        }

        $this->subscribedSmsTypes = array_column($searchResults->getItems(), 'sms_type');

        return $this->subscribedSmsTypes;
    }
}
