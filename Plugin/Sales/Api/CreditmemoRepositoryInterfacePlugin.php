<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Plugin\Sales\Api;

use Linkmobility\Notifications\Model\SmsSender;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;

/**
 * Plug-in for {@see \Magento\Sales\Api\CreditmemoRepositoryInterface}
 *
 * @package Linkmobility\Notifications\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 */
class CreditmemoRepositoryInterfacePlugin
{
    /**
     * @var \Linkmobility\Notifications\Model\SmsSender\CreditmemoSender
     */
    private $smsSender;

    public function __construct(SmsSender $smsSender)
    {
        $this->smsSender = $smsSender;
    }

    public function afterSave(CreditmemoRepositoryInterface $subject, CreditmemoInterface $creditmemo): CreditmemoInterface
    {
        $this->smsSender->send($creditmemo);

        return $creditmemo;
    }
}
