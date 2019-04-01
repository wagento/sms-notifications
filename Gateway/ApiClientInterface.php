<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Gateway;

use LinkMobility\SMSNotifications\Gateway\Entity\ResultInterface;

/**
 * API Client Interface
 *
 * @package LinkMobility\SMSNotifications\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface ApiClientInterface
{
    public const HTTP_METHOD_GET = 'GET';
    public const HTTP_METHOD_POST = 'POST';

    public function setUri(string $uri): void;

    public function setUsername(string $username): void;

    public function setPassword(string $password): void;

    /**
     * @param string[]|\LinkMobility\SMSNotifications\Gateway\Entity\MessageInterface $data
     */
    public function setData($data): void;

    public function setHeaders(array $headers): void;

    public function setHttpMethod(string $httpMethod): void;

    /**
     * @throws \LinkMobility\SMSNotifications\Gateway\ApiException
     */
    public function sendRequest(): void;

    public function getResult(): ResultInterface;
}
