<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Test\Integration
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Test\Integration;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Module Registration Test
 *
 * @package LinkMobility\SMSNotifications\Test\Integration
 * @author Joseph Leedy <joseph@wagento.com>
 */
class RegistrationTest extends TestCase
{
    public function testModuleIsRegistered(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var \Magento\Framework\Component\ComponentRegistrar $componentRegistrar */
        $componentRegistrar = $objectManager->get(ComponentRegistrar::class);
        $paths = $componentRegistrar->getPaths(ComponentRegistrar::MODULE);

        $this->assertArrayHasKey('LinkMobility_SMSNotifications', $paths);
    }
}
