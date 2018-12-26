<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Controller\Adminhtml\Subscription
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Controller\Adminhtml\Subscription;

use Linkmobility\Notifications\Api\Data\SmsSubscriptionInterfaceFactory;
use Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Create SMS Subscription Action
 *
 * @package Linkmobility\Notifications\Controller\Adminhtml\Subscription
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Create extends Action
{
    const ADMIN_RESOURCE = 'Linkmobility_Notifications::sms_subscriptions';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface
     */
    private $smsSubscriptionRepository;
    /**
     * @var \Linkmobility\Notifications\Api\Data\SmsSubscriptionInterfaceFactory
     */
    private $smsSubscriptionFactory;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        SmsSubscriptionRepositoryInterface $smsSubscriptionRepository,
        SmsSubscriptionInterfaceFactory $smsSubscriptionFactory
    ) {
        parent::__construct($context);

        $this->logger = $logger;
        $this->smsSubscriptionRepository = $smsSubscriptionRepository;
        $this->smsSubscriptionFactory = $smsSubscriptionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            if (
                !$this->_getSession()->hasCustomerData()
                || !array_key_exists('customer_id', $this->_getSession()->getCustomerData())
            ) {
                throw new LocalizedException(__('Could not get ID of customer to subscribe SMS notification for.'));
            }

            $customerId = (string)$this->_getSession()->getCustomerData()['customer_id'];
            $smsType = $this->getRequest()->getParam('sms_type');
            $smsSubscription = $this->smsSubscriptionFactory->create();

            $smsSubscription->setCustomerId($customerId);
            $smsSubscription->setSmsType($smsType);

            $this->smsSubscriptionRepository->save($smsSubscription);
            $this->messageManager->addSuccessMessage(
                __('The customer has been subscribed to the SMS notification.')
            );

            $resultRedirect->setPath('customer/index/edit', ['id' => $customerId, '_current' => true]);
        } catch (CouldNotSaveException | LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while subscribing the customer to the SMS notification.')
            );
            $this->logger->critical(
                __('Could not subscribe SMS notification for customer. Error: %1', $e->getMessage())
            );

            if ($e instanceof CouldNotSaveException) {
                $resultRedirect->setPath('customer/index/edit', ['id' => $customerId, '_current' => true]);
            } else {
                $resultRedirect->setPath('customer/index/index');
            }
        }

        return $resultRedirect;
    }
}
