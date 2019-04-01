<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Model;

use Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionInterface;
use Wagento\LinkMobilitySMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use Wagento\LinkMobilitySMSNotifications\Model\SmsSubscriptionFactory as SmsSubscriptionModelFactory;
use Wagento\LinkMobilitySMSNotifications\Model\ResourceModel\SmsSubscription as SmsSubscriptionResourceModel;
use Wagento\LinkMobilitySMSNotifications\Model\ResourceModel\SmsSubscription\CollectionFactory as SmsSubscriptionCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * SMS Subscription Repository
 *
 * @package Wagento\LinkMobilitySMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsSubscriptionRepository implements SmsSubscriptionRepositoryInterface
{
    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Wagento\LinkMobilitySMSNotifications\Model\SmsSubscriptionFactory
     */
    private $smsSubscriptionModelFactory;
    /**
     * @var \Wagento\LinkMobilitySMSNotifications\Model\ResourceModel\SmsSubscription\CollectionFactory
     */
    private $smsSubscriptionCollectionFactory;
    /**
     * @var \Wagento\LinkMobilitySMSNotifications\Model\ResourceModel\SmsSubscription
     */
    private $smsSubscriptionResourceModel;

    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SmsSubscriptionModelFactory $smsSubscriptionModelFactory,
        SmsSubscriptionCollectionFactory $smsSubscriptionCollectionFactory,
        SmsSubscriptionResourceModel $smsSubscriptionResourceModel
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->smsSubscriptionModelFactory = $smsSubscriptionModelFactory;
        $this->smsSubscriptionCollectionFactory = $smsSubscriptionCollectionFactory;
        $this->smsSubscriptionResourceModel = $smsSubscriptionResourceModel;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param int $id
     * @return \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $id): SmsSubscriptionInterface
    {
        /** @var \Wagento\LinkMobilitySMSNotifications\Model\SmsSubscription $smsSubscriptionModel */
        $smsSubscriptionModel = $this->smsSubscriptionModelFactory->create();

        $this->smsSubscriptionResourceModel->load($smsSubscriptionModel, $id);

        if (!$smsSubscriptionModel->getId()) {
            throw new NoSuchEntityException(__('SMS Notification Subscription with ID "%1" does not exist.', $id));
        }

        return $smsSubscriptionModel->getDataModel();
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Wagento\LinkMobilitySMSNotifications\Model\ResourceModel\SmsSubscription\Collection $smsSubscriptionCollection */
        $smsSubscriptionCollection = $this->smsSubscriptionCollectionFactory->create();
        $filterGroups = $searchCriteria->getFilterGroups();

        foreach ($filterGroups as $filterGroup) {
            $fields = [];
            $conditions = [];
            $filters = $filterGroup->getFilters();

            foreach ($filters as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }

            if (count($fields) > 0) {
                $smsSubscriptionCollection->addFieldToFilter($fields, $conditions);
            }
        }

        $searchResults->setTotalCount($smsSubscriptionCollection->getSize());

        /** @var \Magento\Framework\Api\SortOrder[] $sortOrders */
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders !== null) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $smsSubscriptionCollection->addOrder(
                    $sortOrder->getField(),
                    $sortOrder->getDirection()
                );
            }
        }

        $smsSubscriptionCollection->setCurPage($searchCriteria->getCurrentPage());
        $smsSubscriptionCollection->setPageSize($searchCriteria->getPageSize());

        $smsSubscriptions = [];

        foreach ($smsSubscriptionCollection as $smsSubscriptionModel) {
            $smsSubscriptions[] = $smsSubscriptionModel;
        }

        $searchResults->setItems($smsSubscriptions);

        return $searchResults;
    }

    /**
     * @param int $customerId
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getListByCustomerId(int $customerId): SearchResultsInterface
    {
        return $this->getList($this->searchCriteriaBuilder->addFilter('customer_id', $customerId)->create());
    }

    /**
     * @param \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionInterface $smsSubscription
     * @return \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(SmsSubscriptionInterface $smsSubscription): SmsSubscriptionInterface
    {
        try {
            /** @var \Wagento\LinkMobilitySMSNotifications\Model\SmsSubscription $smsSubscriptionModel */
            $smsSubscriptionModel = $this->smsSubscriptionModelFactory->create();

            $smsSubscriptionModel->updateData($smsSubscription);

            $this->smsSubscriptionResourceModel->save($smsSubscriptionModel);

            $savedSmsSubscription = $this->get((int)$smsSubscriptionModel->getId());
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $savedSmsSubscription;
    }

    /**
     * @param \Wagento\LinkMobilitySMSNotifications\Api\Data\SmsSubscriptionInterface $smsSubscription
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(SmsSubscriptionInterface $smsSubscription): bool
    {
        try {
            /** @var \Wagento\LinkMobilitySMSNotifications\Model\SmsSubscription $smsSubscriptionModel */
            $smsSubscriptionModel = $this->smsSubscriptionModelFactory->create();

            $smsSubscriptionModel->updateData($smsSubscription);

            $this->smsSubscriptionResourceModel->delete($smsSubscriptionModel);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool
    {
        return $this->delete($this->get($id));
    }
}
