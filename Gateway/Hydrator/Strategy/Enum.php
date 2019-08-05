<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Gateway\Hydrator\Strategy
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Gateway\Hydrator\Strategy;

use Zend\Hydrator\Exception\InvalidArgumentException;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * ENUM Hydrator Strategy
 *
 * @package Wagento\SMSNotifications\Gateway\Hydrator\Strategy
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Magento2.PHP.FinalImplementation.FoundFinal -- This class is not meant to be extended. Inherit from
 * its interface instead.
 */
final class Enum implements StrategyInterface
{
    /**
     * @var string
     */
    private $enumClass;

    public function __construct(string $enumClass)
    {
        if (!\is_subclass_of($enumClass, \MyCLabs\Enum\Enum::class)) {
            throw new InvalidArgumentException('Parameter enumClass must be an instance of \MyCLabs\Enum\Enum.');
        }

        $this->enumClass = $enumClass;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($value)
    {
        if ($value === null) {
            return $value;
        }

        if (!$value instanceof \MyCLabs\Enum\Enum) {
            throw new InvalidArgumentException(\sprintf(
                'Unable to extract. Expected instance of \MyCLabs\Enum\Enum. %s was given.',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return $value->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($value)
    {
        try {
            $enum = new $this->enumClass($value);
        } catch (\UnexpectedValueException $e) {
            throw new InvalidArgumentException(
                \sprintf('Unable to hydrate. Received error: %s', $e->getMessage()),
                0,
                $e
            );
        }

        return $enum;
    }
}
