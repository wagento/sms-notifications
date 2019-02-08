<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\ViewModel
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\ViewModel;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Mobile Telephone View Model
 *
 * @package Linkmobility\Notifications\ViewModel
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class MobileTelephone implements ArgumentInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function getPrefix(): ?string
    {
        $mobilePrefixAttribute = $this->customerSession->getCustomerData()
            ->getCustomAttribute('sms_mobile_phone_prefix');

        if ($mobilePrefixAttribute === null) {
            return null;
        }

        return $mobilePrefixAttribute->getValue();
    }

    public function getNumber(): ?string
    {
        $mobileNumberAttribute = $this->customerSession->getCustomerData()
            ->getCustomAttribute('sms_mobile_phone_number');

        if ($mobileNumberAttribute === null) {
            return null;
        }

        return $mobileNumberAttribute->getValue();
    }
}
