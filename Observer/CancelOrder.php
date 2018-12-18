<?php
namespace Linkmobility\Notifications\Observer;


class CancelOrder  implements \Magento\Framework\Event\ObserverInterface {

    protected $_logger;
    protected $_sender;
    protected $_scopeConfig;


    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Linkmobility\Notifications\Model\Api\Sms\Send $sender
    ) {
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_sender = $sender;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $address = ($order->getShippingAddress() ? : $order->getBillingAddress());
        $telephone = ($address ? $address->getTelephone() : NULL);

        $this->_sender
            ->setSource(
                $this->_scopeConfig->getValue("linkmobility/sms_notifications/source_number")
            )
            ->setDestination($telephone)
            ->setUserData(
                "Your order number {$order->getIncrementId()} has been canceled successfully. Thank you."
            );
        $this->_logger->info("Linkmobility: preparing request");
        try {
            $response = $this->_sender->execute();
            $this->_logger->info("Linkmobility: response received");
            $this->_logger->info(print_r($response, TRUE));
        }catch (\Exception $e){
            $this->_logger->info($e->getMessage());
        }

        return $this;
    }
}