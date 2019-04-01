<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Traits
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Traits;

/**
 * Data Object Magic Methods
 *
 * @package Wagento\LinkMobilitySMSNotifications\Traits
 * @author Joseph Leedy <joseph@wagento.com>
 */
trait DataObjectMagicMethods
{
    /**
     * @param mixed $value
     */
    public function __set(string $key, $value): void
    {
        $this->setData($key, $value);
    }

    /**
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getData($key);
    }

    public function __isset(string $key): bool
    {
        return $this->hasData($key);
    }

    public function __unset(string $key): void
    {
        $this->unsetData($key);
    }
}
