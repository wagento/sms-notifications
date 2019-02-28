<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Controller\SmsNotifications
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Controller\SmsNotifications;

use LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterfaceFactory;
use LinkMobility\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Psr\Log\LoggerInterface;

/**
 * Manage SMS Subscriptions POST Action Controller
 *
 * @package LinkMobility\SMSNotifications\Controller\SmsNotifications
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ManagePost extends Action implements ActionInterface, CsrfAwareActionInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \LinkMobility\SMSNotifications\Api\SmsSubscriptionRepositoryInterface
     */
    private $smsSubscriptionRepository;
    /**
     * @var \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterfaceFactory
     */
    private $smsSubscriptionFactory;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $logger,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SmsSubscriptionRepositoryInterface $smsSubscriptionRepository,
        SmsSubscriptionInterfaceFactory $smsSubscriptionFactory
    ) {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->smsSubscriptionRepository = $smsSubscriptionRepository;
        $this->smsSubscriptionFactory = $smsSubscriptionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerId = $this->customerSession->getCustomerId();
        $selectedSmsTypes = array_keys($this->getRequest()->getParam('sms_types', []));

        $resultRedirect->setPath('*/*/manage');

        if ($customerId === null) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving your text notification preferences.')
            );
            $this->logger->critical(__('Could not get ID of customer to save SMS preferences for.'));

            return $resultRedirect;
        }

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customerId)->create();
        $subscribedSmsTypes = $this->smsSubscriptionRepository->getList($searchCriteria)->getItems();

        if (count($subscribedSmsTypes) > 0) {
            $this->removeSubscriptions($subscribedSmsTypes, $selectedSmsTypes, $customerId);

            $selectedSmsTypes = array_diff($selectedSmsTypes, array_column($subscribedSmsTypes, 'sms_type'));
        }

        if (count($selectedSmsTypes) > 0) {
            $this->createSubscriptions($selectedSmsTypes, $customerId);
        }

        $this->updateMobileTelephoneNumber();

        return $resultRedirect;
   }

    /**
     * {@inheritdoc}
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $resultRedirect->setPath('*/*/manage');

        return new InvalidRequestException($resultRedirect, [__('Invalid Form Key. Please refresh the page.')]);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return null;
    }

    /**
     * @param \LinkMobility\SMSNotifications\Model\SmsSubscription[] $subscribedSmsTypes
     * @param string[] $selectedSmsTypes
     * @param string|int $customerId
     */
    private function removeSubscriptions(array &$subscribedSmsTypes, array $selectedSmsTypes, $customerId): int
    {
        $removedSubscriptions = 0;

        foreach ($subscribedSmsTypes as $key => $subscribedSmsType) {
            if (in_array($subscribedSmsType->getSmsType(), $selectedSmsTypes, true)) {
                continue;
            }

            try {
                $this->smsSubscriptionRepository->deleteById((int)$subscribedSmsType->getId());

                ++$removedSubscriptions;

                unset($subscribedSmsTypes[$key]);
            } catch (NoSuchEntityException | CouldNotDeleteException $e) {
                $this->logger->critical(
                    __('Could not delete SMS subscription for customer. Error: %1', $e->getMessage()),
                    [
                        'customer_id' => $customerId,
                        'sms_type' => $subscribedSmsType->getSmsType(),
                        'area' => 'frontend'
                    ]
                );
            }
        }

        $remainingSubscriptions = array_diff(array_column($subscribedSmsTypes, 'sms_type'), $selectedSmsTypes);
        $remainingSubscriptionCount = count($remainingSubscriptions) - $removedSubscriptions;

        if ($remainingSubscriptionCount === 1) {
            $this->messageManager->addErrorMessage(__('You could not be unsubscribed from 1 text notification.'));
        }

        if ($remainingSubscriptionCount > 1) {
            $this->messageManager->addErrorMessage(
                __('You could not be unsubscribed from %1 text notifications.', $remainingSubscriptionCount)
            );
        }

        if ($removedSubscriptions === 1) {
            $this->messageManager->addSuccessMessage(__('You have been unsubscribed from 1 text notification.'));
        }

        if ($removedSubscriptions > 1) {
            $this->messageManager->addSuccessMessage(
                __('You have been unsubscribed from %1 text notifications.', $removedSubscriptions)
            );
        }

        return $removedSubscriptions;
    }

    /**
     * @param string[] $selectedSmsTypes
     * @param string|int $customerId
     */
    private function createSubscriptions(array $selectedSmsTypes, $customerId): int
    {
        $createdSubscriptions = 0;

        foreach ($selectedSmsTypes as $smsType) {
            /** @var \LinkMobility\SMSNotifications\Api\Data\SmsSubscriptionInterface $smsSubscription */
            $smsSubscription = $this->smsSubscriptionFactory->create();

            $smsSubscription->setCustomerId((int)$customerId);
            $smsSubscription->setSmsType($smsType);

            try {
                $this->smsSubscriptionRepository->save($smsSubscription);

                ++$createdSubscriptions;
            } catch (CouldNotSaveException $e) {
                $this->logger->critical(
                    __('Could not subscribe customer to SMS notification. Error: %1', $e->getMessage()),
                    [
                        'customer_id' => $customerId,
                        'sms_type' => $smsType,
                        'area' => 'frontend'
                    ]
                );
            }
        }

        $remainingSubscriptions = count($selectedSmsTypes) - $createdSubscriptions;

        if ($remainingSubscriptions === 1) {
            $this->messageManager->addErrorMessage(__('You could not be subscribed to 1 text notification.'));
        }

        if ($remainingSubscriptions > 1)   {
            $this->messageManager->addErrorMessage(
                __('You could not be subscribed to %1 text notifications.', $remainingSubscriptions)
            );
        }

        if ($createdSubscriptions === 1) {
            $this->messageManager->addSuccessMessage(__('You have been subscribed to 1 text notification.'));
        }

        if ($createdSubscriptions > 1) {
            $this->messageManager->addSuccessMessage(
                __('You have been subscribed to %1 text notifications.', $createdSubscriptions)
            );
        }

        return $createdSubscriptions;
    }

    private function updateMobileTelephoneNumber(): void
    {
        $customer = $this->customerSession->getCustomerDataObject();
        $newMobileTelephonePrefix = $this->getRequest()->getParam('sms_mobile_phone_prefix');
        $existingMobileTelephonePrefix = $this->customerSession->getCustomerDataObject()
            ->getCustomAttribute('sms_mobile_phone_prefix');
        $newMobileTelephoneNumber = $this->getRequest()->getParam('sms_mobile_phone_number');
        $existingMobileTelephoneNumber = $this->customerSession->getCustomerDataObject()
            ->getCustomAttribute('sms_mobile_phone_number');
        $haveChanges = false;

        if (!empty($newMobileTelephonePrefix) && empty($newMobileTelephoneNumber)) {
            return;
        }

        if ($existingMobileTelephonePrefix !== $newMobileTelephonePrefix) {
            $customer->setCustomAttribute('sms_mobile_phone_prefix', $newMobileTelephonePrefix);

            $haveChanges = true;
        }

        if ($existingMobileTelephoneNumber !== $newMobileTelephoneNumber) {
            $customer->setCustomAttribute('sms_mobile_phone_number', $newMobileTelephoneNumber);

            $haveChanges = true;
        }

        if (!$haveChanges) {
            return;
        }

        try {
            $this->customerRepository->save($customer);
        } catch (InputException | InputMismatchException | LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('Your mobile telephone number could not be updated.'));
            $this->logger->critical(
                __('Could not save mobile telephone number. Error: %1', $e->getMessage()),
                [
                    'customer_id' => $customer->getId(),
                    'mobile_phone_prefix' => $newMobileTelephonePrefix,
                    'mobile_phone_number' => $newMobileTelephoneNumber
                ]
            );

            return;
        }

        $this->messageManager->addSuccessMessage(__('Your mobile telephone number has been updated.'));
    }
}
