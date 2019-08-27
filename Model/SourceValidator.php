<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Model;

use Wagento\SMSNotifications\Api\ValidationRulesInterface;
use Wagento\SMSNotifications\Model\Config\Backend\Source;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Validator\DataObject as ValidatorObject;
use Magento\Framework\Validator\DataObjectFactory as ValidatorObjectFactory;

/**
 * Source Configuration Field Validator
 *
 * @package Wagento\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Magento2.PHP.FinalImplementation.FoundFinal -- This validator is not meant to be extended.
 */
final class SourceValidator extends Validator
{
    /**
     * @var string
     */
    private $sourceType;

    public function __construct(
        ValidatorObjectFactory $validatorObjectFactory,
        ValidationRulesInterface $validationRules
    ) {
        if (!$validationRules instanceof SourceValidationRules) {
            throw new \InvalidArgumentException(
                (string)__('Validation Rules object must be an instance of SourceValidationRules.')
            );
        }

        parent::__construct($validatorObjectFactory, $validationRules);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    public function validate(AbstractModel $model): void
    {
        if (!$model instanceof Source) {
            throw new \InvalidArgumentException((string)__('Model to validate must be an instance of Source.'));
        }

        parent::validate($model);
    }

    public function setSourceType(string $sourceType): self
    {
        $this->sourceType = $sourceType;

        return $this;
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    protected function addValidationRules(ValidatorObject $validator): void
    {
        $this->validationRules->addSourceIsValidRules($validator, $this->sourceType);
    }
}
