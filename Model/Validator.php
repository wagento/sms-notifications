<?php
/**
 * LINK Mobility SMS Notifications
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
use Wagento\SMSNotifications\Api\ValidatorInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Validator\DataObject as ValidatorObject;
use Magento\Framework\Validator\DataObjectFactory as ValidatorObjectFactory;

/**
 * Base Model Validator
 *
 * @package Wagento\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @api
 */
abstract class Validator implements ValidatorInterface
{
    /**
     * @var \Magento\Framework\Validator\DataObjectFactory
     */
    protected $validatorObjectFactory;
    /**
     * @var \Wagento\SMSNotifications\Api\ValidationRulesInterface
     */
    protected $validationRules;
    protected $isValid = true;
    /**
     * @var string[]
     */
    protected $messages = [];

    public function __construct(
        ValidatorObjectFactory $validatorObjectFactory,
        ValidationRulesInterface $validationRules
    ) {
        $this->validatorObjectFactory = $validatorObjectFactory;
        $this->validationRules = $validationRules;
    }

    /**
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    public function validate(AbstractModel $model): void
    {
        /** @var \Magento\Framework\Validator\DataObject $validator */
        $validator = $this->getValidator();

        if (!$validator->isValid($model)) {
            $this->messages = $validator->getMessages();
            $this->isValid = false;
        }
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @throws \Zend_Validate_Exception
     */
    public function getValidator(): \Zend_Validate_Interface
    {
        /** @var \Magento\Framework\Validator\DataObject $validator */
        $validator = $this->validatorObjectFactory->create();

        $this->addValidationRules($validator);

        return $validator;
    }

    abstract protected function addValidationRules(ValidatorObject $validator): void;
}
