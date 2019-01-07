<?php

namespace Linkmobility\Notifications\Observer;

use Linkmobility\Notifications\Api\ConfigInterface;
use Linkmobility\Notifications\Model\MessageService;

class UpdateOrder  implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Linkmobility\Notifications\Api\ConfigInterface
     */
    private $config;
    /**
     * @var \Linkmobility\Notifications\Model\MessageService
     */
    private $messageService;

    public function __construct(
        ConfigInterface $config,
        MessageService $messageService
    ) {
        $this->config = $config;
        $this->messageService = $messageService;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $address = ($order->getShippingAddress() ? : $order->getBillingAddress());
        $telephone = ($address ? $address->getTelephone() : null);

        if ($order->getStatus() === 'canceled') {
            return $this;
        }

        if ($telephone === null) {
            return $this;
        }

        $this->messageService->setOrder($order);
        $this->messageService->sendMessage($this->config->getOrderUpdatedTemplate(), $telephone, 'order');

        return $this;
    }
}
