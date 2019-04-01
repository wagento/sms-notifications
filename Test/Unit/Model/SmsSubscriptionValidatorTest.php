<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\LinkMobilitySMSNotifications\Test\Unit\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\LinkMobilitySMSNotifications\Test\Unit\Model;

use Wagento\LinkMobilitySMSNotifications\Model\SmsSubscription;
use Wagento\LinkMobilitySMSNotifications\Model\SmsSubscriptionValidationRules;
use Wagento\LinkMobilitySMSNotifications\Model\SmsSubscriptionValidator;
use Wagento\LinkMobilitySMSNotifications\Model\Source\SmsType;
use Magento\Framework\Validator\DataObject;
use Magento\Framework\Validator\DataObjectFactory;
use Magento\Framework\Validator\NotEmpty;
use Magento\Framework\Validator\NotEmptyFactory;
use PHPUnit\Framework\TestCase;

/**
 * SMS Subscription Validator Test
 *
 * @package Wagento\LinkMobilitySMSNotifications\Test\Unit\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsSubscriptionValidatorTest extends TestCase
{
    /** @var \Wagento\LinkMobilitySMSNotifications\Model\SmsSubscriptionValidator */
    private $validator;

    public function testValidateValidSubscription(): void
    {
        $smsSubscription = $this->getMockBuilder(SmsSubscription::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getCustomerId',
                'getSmsType',
            ])
            ->getMock();

        $smsSubscription->method('getCustomerId')->willReturn(1);
        $smsSubscription->method('getSmsType')->willReturn('order_placed');

        $this->validator->validate($smsSubscription);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getMessages());
    }

    public function testValidateInvalidSubscription(): void
    {
        $smsSubscription = $this->getMockBuilder(SmsSubscription::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->validator->validate($smsSubscription);

        $this->assertFalse($this->validator->isValid());
        $this->assertNotEmpty($this->validator->getMessages());
    }

    public function testGetValidator(): void
    {
        $this->assertInstanceOf(\Zend_Validate_Interface::class, $this->validator->getValidator());
    }

    protected function setUp()
    {
        parent::setUp();

        $validatorObjectFactory = $this->getMockBuilder(DataObjectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $notEmptyFactory = $this->getMockBuilder(NotEmptyFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $inArrayFactory = $this->getMockBuilder(\Zend_Validate_InArrayFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $validatorObjectFactory->method('create')->willReturn(new DataObject());
        $notEmptyFactory->method('create')->willReturn(new NotEmpty());
        $inArrayFactory->method('create')->willReturnCallback(function ($arguments) {
            return new \Zend_Validate_InArray($arguments['options']);
        });

        $validationRules = new SmsSubscriptionValidationRules($notEmptyFactory, $inArrayFactory, new SmsType());
        $this->validator = new SmsSubscriptionValidator($validatorObjectFactory, $validationRules);
    }
}
