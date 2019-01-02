<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Ui\Component\Form\Fieldset
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Ui\Component\Form\Fieldset;

use Linkmobility\Notifications\Api\ConfigInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset;

/**
 * SMS Subscription Listing Fieldset UI Component
 *
 * @package Linkmobility\Notifications\Ui\Component\Form\Fieldset
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsSubscriptionListingFieldset extends Fieldset
{
    /**
     * @var \Linkmobility\Notifications\Api\ConfigInterface
     */
    private $config;

    public function __construct(
        ContextInterface $context,
        ConfigInterface $config,
        $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();

        if (!$this->config->isEnabled()) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }
}
