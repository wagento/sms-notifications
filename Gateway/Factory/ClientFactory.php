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

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * Client Factory
 *
 * @package LinkMobility\SMSNotifications\Gateway\Factory
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class ClientFactory
{
    public function create(array $config): ClientInterface
    {
        return new Client($config);
    }
}
