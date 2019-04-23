<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Block\Customer\Account\Create
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Block\Customer\Account\Create;

use Wagento\SMSNotifications\Block\AbstractBlock;

/**
 * Subscribe Form Field Block
 *
 * @package Wagento\SMSNotifications\Block\Customer\Account\Create
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SubscribeField extends AbstractBlock
{
    public function isOptinRequired(): bool
    {
        return $this->config->isOptinRequired($this->getWebsiteId());
    }

    public function isTermsAndConditionsShownAfterOptin(): bool
    {
        return $this->config->isTermsAndConditionsShownAfterOptin($this->getWebsiteId());
    }
}
