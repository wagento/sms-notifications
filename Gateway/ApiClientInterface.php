<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Gateway;

use Linkmobility\Notifications\Gateway\Entity\ResultInterface;

/**
 * API Client Interface
 *
 * @package Linkmobility\Notifications\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface ApiClientInterface
{
    public const HTTP_METHOD_GET = 'GET';
    public const HTTP_METHOD_POST = 'POST';

    public function setUri(string $uri): void;

    public function setUsername(string $username): void;

    public function setPassword(string $password): void;

    public function setData(array $data): void;

    public function setHeaders(array $headers): void;

    public function setHttpMethod(string $httpMethod): void;

    /**
     * @throws \Linkmobility\Notifications\Gateway\ApiException
     */
    public function sendRequest(): void;

    public function getResult(): ResultInterface;
}
