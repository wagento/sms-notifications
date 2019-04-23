<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Gateway
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Gateway;

/**
 * API Exception
 *
 * @package Wagento\SMSNotifications\Gateway
 */
class ApiException extends \Exception
{
    private $responseData = [];

    /**
     * @phpcs:disable Generic.Files.LineLength.TooLong
     */
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null, array $responseData = [])
    {
        parent::__construct($message, $code, $previous);

        $this->responseData = $responseData;
    }

    public function getResponseData(): array
    {
        return $this->responseData;
    }
}
