<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Api;

use Magento\Framework\Model\AbstractModel;

/**
 * SMS Subscription Validator Interface
 *
 * @package Wagento\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
interface ValidatorInterface
{
    /**
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    public function validate(AbstractModel $model): void;

    public function isValid(): bool;

    public function getMessages(): array;

    /**
     * @throws \Zend_Validate_Exception
     */
    public function getValidator(): \Zend_Validate_Interface;
}
