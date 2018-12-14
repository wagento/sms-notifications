<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Model\ResourceModel\SmsType
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Model\ResourceModel\SmsType;

use Linkmobility\Notifications\Model\ResourceModel\SmsType as SmsTypeResourceModel;
use Linkmobility\Notifications\Model\SmsType as SmsTypeModel;
use Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection;

/**
 * SMS Type Collection
 *
 * @package Linkmobility\Notifications\Model\ResourceModel\SmsType
 * @author Yair García Torres <@wagento.com>
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(SmsTypeModel::class, SmsTypeResourceModel::class);
    }
}
