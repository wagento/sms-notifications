<?php

namespace Linkmobility\Notifications\Observer;

use Linkmobility\Notifications\Api\ConfigInterface;
use Linkmobility\Notifications\Model\MessageService;

class ShipOrder  implements \Magento\Framework\Event\ObserverInterface
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
        $shipment = $event->getShipment();
        $order = $shipment->getOrder();
        $address = ($order->getShippingAddress() ? : $order->getBillingAddress());
        $telephone = ($address ? $address->getTelephone() : null);

        if ($telephone === null) {
            return $this;
        }

        $this->messageService->setOrder($order);
        $this->messageService->setShipment($shipment);
        $this->messageService->sendMessage($this->config->getOrderShippedTemplate(), $telephone, 'order');

        return $this;
    }
}
