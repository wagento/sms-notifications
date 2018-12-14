<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Model;

use Linkmobility\Notifications\Api\Data\SmsTypeInterface;
use Linkmobility\Notifications\Api\SmsTypeRepositoryInterface;
use Linkmobility\Notifications\Model\SmsTypeFactory as SmsTypeModelFactory;
use Linkmobility\Notifications\Model\ResourceModel\SmsType as SmsTypeResourceModel;
use Linkmobility\Notifications\Model\ResourceModel\SmsType\CollectionFactory as SmsTypeCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * SMS Type Repository
 *
 * @package Linkmobility\Notifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsTypeRepository implements SmsTypeRepositoryInterface
{
    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var \Linkmobility\Notifications\Model\SmsTypeFactory
     */
    private $smsTypeModelFactory;
    /**
     * @var \Linkmobility\Notifications\Model\ResourceModel\SmsType\CollectionFactory
     */
    private $smsTypeCollectionFactory;
    /**
     * @var \Linkmobility\Notifications\Model\ResourceModel\SmsType
     */
    private $smsTypeResourceModel;

    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SmsTypeModelFactory $smsTypeModelFactory,
        SmsTypeCollectionFactory $smsTypeCollectionFactory,
        SmsTypeResourceModel $smsTypeResourceModel
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->smsTypeModelFactory = $smsTypeModelFactory;
        $this->smsTypeCollectionFactory = $smsTypeCollectionFactory;
        $this->smsTypeResourceModel = $smsTypeResourceModel;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $id): SmsTypeInterface
    {
        /** @var \Linkmobility\Notifications\Model\SmsType $smsTypeModel */
        $smsTypeModel = $this->smsTypeModelFactory->create();

        $this->smsTypeResourceModel->load($smsTypeModel, $id);

        if (!$smsTypeModel->getId()) {
            throw new NoSuchEntityException(__('SMS Notification Type with ID "%1" does not exist.', $id));
        }

        return $smsTypeModel->getDataModel();
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Linkmobility\Notifications\Model\ResourceModel\SmsType\Collection $smsTypeCollection */
        $smsTypeCollection = $this->smsTypeCollectionFactory->create();
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
                $smsTypeCollection->addFieldToFilter($fields, $conditions);
            }
        }

        $searchResults->setTotalCount($smsTypeCollection->getSize());

        /** @var \Magento\Framework\Api\SortOrder[] $sortOrders */
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders !== null) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $smsTypeCollection->addOrder(
                    $sortOrder->getField(),
                    $sortOrder->getDirection()
                );
            }
        }

        $smsTypeCollection->setCurPage($searchCriteria->getCurrentPage());
        $smsTypeCollection->setPageSize($searchCriteria->getPageSize());

        $smsTypes = [];

        foreach ($smsTypeCollection as $smsTypeModel) {
            $smsTypes[] = $smsTypeModel;
        }

        $searchResults->setItems($smsTypes);

        return $searchResults;
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(SmsTypeInterface $smsType): SmsTypeInterface
    {
        try {
            /** @var \Linkmobility\Notifications\Model\SmsType $smsTypeModel */
            $smsTypeModel = $this->smsTypeModelFactory->create();

            $smsTypeModel->updateData($smsType);

            $this->smsTypeResourceModel->save($smsTypeModel);

            $savedMembership = $this->get($smsType->getSmsTypeId());
        } catch(\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $savedMembership;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(SmsTypeInterface $smsType): bool
    {
        try {
            /** @var \Linkmobility\Notifications\Model\SmsType $smsTypeModel */
            $smsTypeModel = $this->smsTypeModelFactory->create();

            $smsTypeModel->updateData($smsType);

            $this->smsTypeResourceModel->delete($smsTypeModel);
        } catch(\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }

        return true;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool
    {
        return $this->delete($this->get($id));
    }
}
