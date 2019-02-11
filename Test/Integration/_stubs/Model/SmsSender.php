<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Test\Integration\_stubs\Model;

/**
 * Concrete Implementation of SMS Sender
 *
 * @package Linkmobility\Notifications\Test\Integration\_stubs\Model
 *
 * @codeCoverageIgnore
 */
class SmsSender extends \Linkmobility\Notifications\Model\SmsSender
{
    public function getCustomerMobilePhoneNumber(int $customerId): ?string
    {
        return parent::getCustomerMobilePhoneNumber($customerId);
    }

    public function getCustomerSmsSubscriptions(int $customerId): array
    {
        return parent::getCustomerSmsSubscriptions($customerId);
    }
}
