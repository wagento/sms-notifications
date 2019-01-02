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

use Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Delete SMS Subscription Action
 *
 * @package Linkmobility\Notifications\Controller\Adminhtml\Subscription
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Delete extends Action
{
    const ADMIN_RESOURCE = 'Linkmobility_Notifications::manage_sms_subscriptions';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface
     */
    private $smsSubscriptionRepository;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        SmsSubscriptionRepositoryInterface $smsSubscriptionRepository
    ) {
        parent::__construct($context);

        $this->logger = $logger;
        $this->smsSubscriptionRepository = $smsSubscriptionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (
            $this->_getSession()->hasCustomerData()
            && array_key_exists('customer_id', $this->_getSession()->getCustomerData())
        ) {
            $customerId = $this->_getSession()->getCustomerData()['customer_id'];
            $resultRedirect->setPath('customer/index/edit', ['id' => $customerId, '_current' => true]);
        } else {
            $resultRedirect->setPath('customer/index/index');
        }

        try {
            $this->smsSubscriptionRepository->deleteById((int)$this->getRequest()->getParam('sms_subscription_id'));
            $this->messageManager->addSuccessMessage(
                __('The customer has been unsubscribed from the SMS notification.')
            );
        } catch (CouldNotDeleteException | NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while unsubscribing the customer from the SMS notification.')
            );
            $this->logger->critical(
                __(
                    'Could not unsubscribe SMS notification for customer with ID "%1". Error: %2',
                    $customerId,
                    $e->getMessage()
                )
            );
        }

        return $resultRedirect;
    }
}
