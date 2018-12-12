<?php
namespace Linkmobility\Notifications\Observer;


class CreateOrder  implements \Magento\Framework\Event\ObserverInterface {

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
        $active = $this->_scopeConfig->getValue("customer/linkmobility_notifications/active");

        if ($active) {
            $this->_sender
                ->setSource(
                    $this->_scopeConfig->getValue("customer/linkmobility_notifications/source_number")
                )
                ->setDestination($order->getShippingAddress()->getTelephone())
                ->setUserData(
                    "Your order number {$order->getIncrementalId()} was received successfully."
                );
            $this->_logger->debug("Linkmobility: preparing request");
            try {
                $response = $this->_sender->execute();
                $this->_logger->debug("Linkmobility: response received");
                $this->_logger->debug(print_r($response, TRUE));
            }catch (\Exception $e){
                $this->_logger->debug($e->getMessage());
            }
        }

        return $this;
    }
}