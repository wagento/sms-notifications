<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Observer
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Observer;

use Linkmobility\Notifications\Api\ConfigInterface;
use Linkmobility\Notifications\Api\Data\SmsSubscriptionInterfaceFactory;
use Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface;
use Linkmobility\Notifications\Model\Source\SmsType as SmsTypeSource;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Observer for customer_register_success event
 *
 * @package Linkmobility\Notifications\Observer
 * @author Joseph Leedy <joseph@wagento.com>
 * @see \Magento\Customer\Controller\Account\CreatePost::execute()
 */
class CustomerRegisterSuccessObserver implements ObserverInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Linkmobility\Notifications\Api\ConfigInterface
     */
    private $config;
    /**
     * @var \Linkmobility\Notifications\Model\Source\SmsType
     */
    private $smsTypeSource;
    /**
     * @var \Linkmobility\Notifications\Model\SmsSubscriptionFactory
     */
    private $smsSubscriptionFactory;
    /**
     * @var \Linkmobility\Notifications\Api\SmsSubscriptionRepositoryInterface
     */
    private $smsSubscriptionRepository;

    public function __construct(
        LoggerInterface $logger,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        ConfigInterface $config,
        SmsTypeSource $smsTypeSource,
        SmsSubscriptionInterfaceFactory $smsSubscriptionFactory,
        SmsSubscriptionRepositoryInterface $smsSubscriptionRepository
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->config = $config;
        $this->smsTypeSource = $smsTypeSource;
        $this->smsSubscriptionFactory = $smsSubscriptionFactory;
        $this->smsSubscriptionRepository = $smsSubscriptionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getData('customer');
        $smsNotificationsParameters = $this->request->getParam('sms_notifications', []);

        try {
            $websiteId = (string)$this->storeManager->getStore()->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        if (
            !$this->config->isEnabled($websiteId)
            || !$this->request->isPost()
            || empty($smsNotificationsParameters)
            || empty($smsNotificationsParameters['subscribed'])
        ) {
            return;
        }

        if (
            array_key_exists('sms_types', $smsNotificationsParameters)
            && trim($smsNotificationsParameters['sms_types']) !== ''
        ) {
            $smsTypes = explode(',', $smsNotificationsParameters['sms_types']);
        } else {
            $smsTypes = array_column($this->smsTypeSource->toArray(), 'code');
        }

        foreach ($smsTypes as $smsType) {
            try {
                /** @var \Linkmobility\Notifications\Api\Data\SmsSubscriptionInterface $subscription */
                $subscription = $this->smsSubscriptionFactory->create();

                $subscription->setSmsType($smsType);
                $subscription->setCustomerId($customer->getId());

                $this->smsSubscriptionRepository->save($subscription);
            } catch (CouldNotSaveException $e) {
                $this->logger->critical(
                    __('Could not subscribe customer to SMS notification. Error: %1', $e->getMessage()),
                    [
                        'sms_type' => $smsType,
                        'customer_id' => $customer->getId(),
                        'action' => 'register'
                    ]
                );
            }
        }
    }
}
