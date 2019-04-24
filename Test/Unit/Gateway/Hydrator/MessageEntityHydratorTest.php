<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Test\Unit\Gateway\Hydrator
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Test\Unit\Gateway\Hydrator;

use Wagento\SMSNotifications\Gateway\Entity\Message as MessageEntity;
use Wagento\SMSNotifications\Gateway\Factory\MessageEntityHydratorFactory;
use PHPUnit\Framework\TestCase;

/**
 * Message Entity Hydrator Test
 *
 * @package Wagento\SMSNotifications\Test\Unit\Gateway\Hydrator
 * @author Joseph Leedy <joseph@wagento.com>
 */
class MessageEntityHydratorTest extends TestCase
{
    /**
     * @var \Wagento\SMSNotifications\Gateway\Factory\MessageEntityHydratorFactory
     */
    private $messageEntityHydratorFactory;
    /**
     * @var array
     */
    private $messageData;
    /**
     * @var \Wagento\SMSNotifications\Gateway\Entity\MessageInterface
     */
    private $messageEntity;

    public function testExtract(): void
    {
        $messageEntityHydrator = $this->messageEntityHydratorFactory->create();
        $extractedMessageData = $messageEntityHydrator->extract($this->messageEntity);

        $this->assertEquals($this->messageData, \array_filter($extractedMessageData));
    }

    public function testHydrate(): void
    {
        $messageEntityHydrator = $this->messageEntityHydratorFactory->create();
        $hydratedMessageEntity = $messageEntityHydrator->hydrate($this->messageData, new MessageEntity());

        $this->assertEquals($this->messageEntity, $hydratedMessageEntity);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->messageEntityHydratorFactory = new MessageEntityHydratorFactory();
        $this->messageData = [
            'source' => '+15552345678',
            'sourceTON' => 'MSISDN',
            'destination' => '+15556789012',
            'userData' => 'Hello',
            'platformId' => 'ABC123',
            'platformPartnerId' => '123'
        ];
        $this->messageEntity = new MessageEntity(
            $this->messageData['source'],
            $this->messageData['sourceTON'],
            $this->messageData['destination'],
            $this->messageData['userData'],
            $this->messageData['platformId'],
            $this->messageData['platformPartnerId']
        );
    }
}
