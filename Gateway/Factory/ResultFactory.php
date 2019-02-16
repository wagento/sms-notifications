<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Gateway\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Gateway\Factory;

use LinkMobility\SMSNotifications\Gateway\Entity\ErrorResult;
use LinkMobility\SMSNotifications\Gateway\Entity\ResultInterface;
use LinkMobility\SMSNotifications\Gateway\Entity\SuccessResult;

/**
 * Result Factory
 *
 * @package LinkMobility\SMSNotifications\Gateway\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 */
class ResultFactory
{
    public function create(string $type, array $data = []): ResultInterface
    {
        switch ($type) {
            case 'success':
                $class = SuccessResult::class;
                break;
            case 'error':
                $class = ErrorResult::class;
                break;
            default:
                throw new \InvalidArgumentException('Invalid result type.');
        }

        return new $class($data);
    }
}
