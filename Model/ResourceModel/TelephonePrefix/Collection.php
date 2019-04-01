<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model\ResourceModel\TelephonePrefix
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model\ResourceModel\TelephonePrefix;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use LinkMobility\SMSNotifications\Model\TelephonePrefix as TelephonePrefixModel;
use LinkMobility\SMSNotifications\Model\ResourceModel\TelephonePrefix as TelephonePrefixResource;

/**
 * Telephone Prefix Data Collection
 *
 * @package LinkMobility\SMSNotifications\Model\ResourceModel\TelephonePrefix
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'country_code';
    
    protected function _construct()
    {
        $this->_init(TelephonePrefixModel::class, TelephonePrefixResource::class);
    }
}
