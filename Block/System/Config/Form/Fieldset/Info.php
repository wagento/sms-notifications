<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Block\System\Config\Form\Fieldset
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Block\System\Config\Form\Fieldset;

use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Extension Information Configuration Fieldset Block
 *
 * @package Wagento\SMSNotifications\Block\System\Config\Form\Fieldset
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Info extends Fieldset
{
    private const TEMPLATE = 'Wagento_SMSNotifications::system/config/form/fieldset/info.phtml';

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(AbstractElement $element)
    {
        $block = $this->getLayout()
            ->createBlock(Template::class, 'sms_notifications_config_header')
            ->setTemplate(self::TEMPLATE)
            ->setData([
                'info_text' => $element->getComment() ?? ''
            ]);

        return $block->_toHtml();
    }
}
