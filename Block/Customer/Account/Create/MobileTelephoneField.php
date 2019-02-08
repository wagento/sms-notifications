<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Block\Customer\Account\Create
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Block\Customer\Account\Create;

use Linkmobility\Notifications\Block\AbstractBlock;

/**
 * SMS Notifications Mobile Telephone Block
 *
 * @package Linkmobility\Notifications\Block\Customer\Account\Create
 * @author Joseph Leedy <joseph@wagento.com>
 */
class MobileTelephoneField extends AbstractBlock
{
    public function getFieldVisibility(): string
    {
        return $this->getTelephonePrefix() !== null && $this->getTelephoneNumber() !== '' ? 'true' : 'false';
    }

    public function getTelephonePrefix(): ?string
    {
        return $this->getFormData()->getSmsMobilePhonePrefix();
    }

    public function getTelephoneNumber(): string
    {
        return $this->getFormData()->getSmsMobilePhoneNumber() ?? '';
    }
}
