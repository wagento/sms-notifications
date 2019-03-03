<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model\SmsSender;

use LinkMobility\SMSNotifications\Model\SmsSender;
use Magento\Framework\Model\AbstractModel;

/**
 * Welcome SMS Sender
 *
 * @package LinkMobility\SMSNotifications\Model\SmsSender
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
final class WelcomeSender extends SmsSender
{
    /**
     * @phpcs:disable Generic.Files.LineLength.TooLong
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Customer\Api\Data\CustomerInterface|\Magento\Customer\Model\Customer $customer
     */
    public function send(AbstractModel $customer): bool
    {
        $websiteId = (int)$customer->getWebsiteId();

        if (!$this->isModuleEnabled($websiteId) || !$this->config->sendWelcomeMessage($websiteId)) {
            return false;
        }

        $customerData = $customer->getDataModel();
        $messageRecipient = $this->getCustomerMobilePhoneNumber($customerData);

        if ($messageRecipient === null) {
            return false;
        }

        $messageTemplate = $this->config->getWelcomeMessageTemplate((int)$customer->getStoreId());

        $this->messageService->setCustomer($customerData);

        return $this->messageService->sendMessage($messageTemplate, $messageRecipient, 'customer');
    }
}
