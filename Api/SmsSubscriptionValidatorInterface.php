<?php
/**
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Api;

use Linkmobility\Notifications\Model\SmsSubscription;

/**
 * SMS Subscription Validator Interface
 *
 * @package Linkmobility\Notifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
interface SmsSubscriptionValidatorInterface
{
    /**
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    public function validate(SmsSubscription $smsSubscription): void;

    public function isValid(): bool;

    public function getMessages(): array;

    /**
     * @throws \Zend_Validate_Exception
     */
    public function getValidator(): \Zend_Validate_Interface;
}
