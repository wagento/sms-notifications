<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Integration\_stubs\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Integration\_stubs\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Concrete Implementation of SMS Sender
 *
 * @package Wagento\SMSNotifications\Test\Integration\_stubs\Model
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @codeCoverageIgnore
 */
class SmsSender extends \Wagento\SMSNotifications\Model\SmsSender
{
    public function send(AbstractModel $entity): bool
    {
        return true;
    }

    public function getCustomerById(int $customerId): ?CustomerInterface
    {
        return parent::getCustomerById($customerId);
    }

    public function getCustomerMobilePhoneNumber(CustomerInterface $customer): ?string
    {
        return parent::getCustomerMobilePhoneNumber($customer);
    }

    public function getCustomerSmsSubscriptions(int $customerId): array
    {
        return parent::getCustomerSmsSubscriptions($customerId);
    }
}
