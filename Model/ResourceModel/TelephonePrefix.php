<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Model\ResourceModel
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Telephone Prefix Data Resource Model
 *
 * @package Wagento\SMSNotifications\Model\ResourceModel
 * @author Joseph Leedy <joseph@wagento.com>
 */
class TelephonePrefix extends AbstractDb
{
    public const TABLE_NAME = 'directory_telephone_prefix';
    public const IDENTITY_COLUMN = 'country_code';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::IDENTITY_COLUMN);
    }

    /**
     * {@inheritdoc}
     *
     * Prevent data from being saved as this entity is read-only.
     */
    public function save(AbstractModel $object)
    {
        return $this;
    }
}
