<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Model\Config\Backend
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Model\Config\Backend;

use LinkMobility\SMSNotifications\Api\ValidatorInterface;
use LinkMobility\SMSNotifications\Model\SourceValidator;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Source Configuration Field Backend Model
 *
 * @package LinkMobility\SMSNotifications\Model\Config\Backend
 * @author Joseph Leedy <joseph@wagento.com>
 */
class Source extends Value
{
    /**
     * @var \LinkMobility\SMSNotifications\Api\ValidatorInterface
     */
    private $validator;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ValidatorInterface $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        if (!$validator instanceof SourceValidator) {
            throw new \InvalidArgumentException((string)__('Validator must be an instance of SourceValidator.'));
        }

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);

        $this->validator = $validator;
    }

    public function getSource(): string
    {
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     * @throws \Zend_Validate_Exception
     */
    protected function _getValidationRulesBeforeSave()
    {
        $sourceType = $this->getFieldsetDataValue('source_type') ?? 'MSISDN';

        $this->validator->setSourceType($sourceType);

        return $this->validator->getValidator();
    }
}
