<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Api;

use Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * SMS Subscription Repository Interface
 *
 * @package Wagento\SMSNotifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
interface SmsSubscriptionRepositoryInterface
{
    /**
     * @param int $id
     * @return \Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $id): SmsSubscriptionInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param int $customerId
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getListByCustomerId(int $customerId): SearchResultsInterface;

    /**
     * @param \Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface $smsSubscription
     * @return \Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(SmsSubscriptionInterface $smsSubscription): SmsSubscriptionInterface;

    /**
     * @param \Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface $smsSubscription
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(SmsSubscriptionInterface $smsSubscription): bool;

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;
}
