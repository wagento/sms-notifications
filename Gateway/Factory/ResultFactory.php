<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Gateway\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Gateway\Factory;

use Wagento\LinkMobilitySMSNotifications\Gateway\Entity\ErrorResult;
use Wagento\LinkMobilitySMSNotifications\Gateway\Entity\ResultInterface;
use Wagento\LinkMobilitySMSNotifications\Gateway\Entity\SuccessResult;

/**
 * Result Factory
 *
 * @package Wagento\LinkMobilitySMSNotifications\Gateway\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class ResultFactory
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
