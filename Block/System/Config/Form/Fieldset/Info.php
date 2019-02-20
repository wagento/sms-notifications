<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Block\System\Config\Form\Fieldset
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Block\System\Config\Form\Fieldset;

use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Header Configuration Fieldset Block
 *
 * @package LinkMobility\SMSNotifications\Block\System\Config\Form\Fieldset
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Info extends Fieldset
{
    private const TEMPLATE = 'LinkMobility_SMSNotifications::system/config/form/fieldset/info.phtml';

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(AbstractElement $element)
    {
        $group = $element->getGroup() ?? [];
        $block = $this->getLayout()
            ->createBlock(Template::class, 'linkmobility_sms_notifications_config_header')
            ->setTemplate(self::TEMPLATE)
            ->setData([
                'info_text' => $element->getComment() ?? '',
                'documentation_url' => $group['help_url'] ?: '#',
                'overview_url' => $group['more_url'] ?: '#',
                'signup_url' => $group['demo_link'] ?: '#',
            ]);

        return $block->_toHtml();
    }
}
