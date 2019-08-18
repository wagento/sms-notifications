<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Controller\SmsNotifications
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Controller\SmsNotifications;

use Wagento\SMSNotifications\Api\MobileTelephoneNumberManagementInterface;
use Wagento\SMSNotifications\Api\SmsSubscriptionManagementInterface;
use Wagento\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use Wagento\SMSNotifications\Model\SmsSender;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Psr\Log\LoggerInterface;

/**
 * Manage SMS Subscriptions POST Action Controller
 *
 * @package Wagento\SMSNotifications\Controller\SmsNotifications
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ManagePost extends Action implements ActionInterface, CsrfAwareActionInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;
    /**
     * @var \Wagento\SMSNotifications\Api\SmsSubscriptionRepositoryInterface
     */
    private $smsSubscriptionRepository;
    /**
     * @var \Wagento\SMSNotifications\Api\SmsSubscriptionManagementInterface
     */
    private $smsSubscriptionManagement;
    /**
     * @var \Wagento\SMSNotifications\Model\SmsSender|\Wagento\SMSNotifications\Model\SmsSender\WelcomeSender
     */
    private $smsSender;
    /**
     * @var \Wagento\SMSNotifications\Api\MobileTelephoneNumberManagementInterface
     */
    private $mobileTelephoneNumberManagement;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        LoggerInterface $logger,
        ProductMetadataInterface $productMetadata,
        FormKeyValidator $formKeyValidator,
        SmsSubscriptionRepositoryInterface $smsSubscriptionRepository,
        SmsSubscriptionManagementInterface $smsSubscriptionManagement,
        MobileTelephoneNumberManagementInterface $mobileTelephoneNumberManagement,
        SmsSender $smsSender
    ) {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->productMetadata = $productMetadata;
        $this->formKeyValidator = $formKeyValidator;
        $this->smsSubscriptionRepository = $smsSubscriptionRepository;
        $this->smsSubscriptionManagement = $smsSubscriptionManagement;
        $this->smsSender = $smsSender;
        $this->mobileTelephoneNumberManagement = $mobileTelephoneNumberManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerId = $this->customerSession->getCustomerId();
        $selectedSmsTypes = $this->getRequest()->getParam('sms_types', []);

        $resultRedirect->setPath('*/*/manage');

        if (version_compare($this->productMetadata->getVersion(), '2.3.0', '<')) {
            $isValidFormKey = $this->formKeyValidator->validate($this->getRequest());

            if (!$isValidFormKey) {
                $this->messageManager->addErrorMessage(__('Invalid Form Key. Please refresh the page.'));

                return $resultRedirect;
            }
        }

        if ($customerId === null) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving your text notification preferences.')
            );
            $this->logger->critical(__('Could not get ID of customer to save SMS preferences for.'));

            return $resultRedirect;
        }

        $subscribedSmsTypes = $this->smsSubscriptionRepository->getListByCustomerId((int)$customerId)->getItems();

        if (count($subscribedSmsTypes) > 0) {
            $this->removeSubscriptions($subscribedSmsTypes, $selectedSmsTypes, $customerId);

            $selectedSmsTypes = array_diff($selectedSmsTypes, array_column($subscribedSmsTypes, 'sms_type'));
        }

        if (count($selectedSmsTypes) > 0) {
            $this->createSubscriptions($selectedSmsTypes, $customerId);
        }

        $mobileNumberUpdated = $this->updateMobileTelephoneNumber();

        if ($mobileNumberUpdated) {
            $this->sendWelcomeMessage();
        }

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
     * @param \Wagento\SMSNotifications\Model\SmsSubscription[] $subscribedSmsTypes
     * @param string[] $selectedSmsTypes
     * @param string|int $customerId
     */
    private function removeSubscriptions(array &$subscribedSmsTypes, array $selectedSmsTypes, $customerId): int
    {
        $messages = [
            'error' => [
                'one' => 'You could not be unsubscribed from 1 text notification.',
                'multiple' => 'You could not be unsubscribed from %1 text notifications.'
            ],
            'success' => [
                'one' => 'You have been unsubscribed from 1 text notification.',
                'multiple' => 'You have been unsubscribed from %1 text notifications.'
            ]
        ];

        return $this->smsSubscriptionManagement->removeSubscriptions(
            $subscribedSmsTypes,
            $selectedSmsTypes,
            (int)$customerId,
            $messages
        );
    }

    /**
     * @param string[] $selectedSmsTypes
     * @param string|int $customerId
     */
    private function createSubscriptions(array $selectedSmsTypes, $customerId): int
    {
        $messages = [
            'error' => [
                'one' => 'You could not be subscribed to 1 text notification.',
                'multiple' => 'You could not be subscribed to %1 text notifications.'
            ],
            'success' => [
                'one' => 'You have been subscribed to 1 text notification.',
                'multiple' => 'You have been subscribed to %1 text notifications.'
            ]
        ];

        return $this->smsSubscriptionManagement->createSubscriptions($selectedSmsTypes, (int)$customerId, $messages);
    }

    private function updateMobileTelephoneNumber(): bool
    {
        $newPrefix = $this->getRequest()->getParam('sms_mobile_phone_prefix', '');
        $newNumber = $this->getRequest()->getParam('sms_mobile_phone_number', '');
        $customer = $this->customerSession->getCustomerDataObject();
        $numberUpdated = $this->mobileTelephoneNumberManagement->updateNumber($newPrefix, $newNumber, $customer);

        if (!$numberUpdated) {
            if ($numberUpdated === false) {
                $this->messageManager->addErrorMessage(__('Your mobile telephone number could not be updated.'));
            }

            return false;
        }

        $this->messageManager->addSuccessMessage(__('Your mobile telephone number has been updated.'));

        return true;
    }

    private function sendWelcomeMessage(): bool
    {
        return $this->smsSender->send($this->customerSession->getCustomer());
    }
}
