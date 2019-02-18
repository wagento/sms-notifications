<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Api;

use LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * SMS Subscription Repository Interface
 *
 * @package LinkMobility\SMSNotifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface SmsSubscriptionRepositoryInterface
{
    /**
     * @param int $id
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $id): SmsSubscriptionInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface $smsSubscription
     * @return \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(SmsSubscriptionInterface $smsSubscription): SmsSubscriptionInterface;

    /**
     * @param \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface $smsSubscription
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
