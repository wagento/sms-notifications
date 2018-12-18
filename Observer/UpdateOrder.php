<?php
namespace Linkmobility\Notifications\Observer;


use Linkmobility\Notifications\Api\ConfigInterface;

class UpdateOrder  implements \Magento\Framework\Event\ObserverInterface {

    protected $_logger;
    protected $_sender;

    /**
     * @var \Linkmobility\Notifications\Api\ConfigInterface
     */
    private $config;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        ConfigInterface $config,
        \Linkmobility\Notifications\Model\Api\Sms\Send $sender
    ) {
        $this->_logger = $logger;
        $this->_sender = $sender;
        $this->config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $event = $observer->getEvent();
        $order = $event->getOrder();

        if ($order->getStatus() !== "canceled") {
            $address = ($order->getShippingAddress() ? : $order->getBillingAddress());
            $telephone = ($address ? $address->getTelephone() : NULL);
            $this->_sender
                ->setSource(
                    $this->config->getSourceNumber()
                )
                ->setDestination($telephone)
                ->setUserData(
                    "Your order number {$order->getIncrementId()} has been updated to status: {$order->getStatus()}, please verify in your account. Thank you."
                );
            $this->_logger->info("Linkmobility: preparing request");
            try {
                $response = $this->_sender->execute();
                $this->_logger->info("Linkmobility: response received");
                $this->_logger->info(print_r($response, TRUE));
            } catch (\Exception $e) {
                $this->_logger->info($e->getMessage());
            }
        }

        return $this;
    }
}