<?php
namespace Linkmobility\Notifications\Observer;


use Linkmobility\Notifications\Api\ConfigInterface;

class RefundOrder implements \Magento\Framework\Event\ObserverInterface
{

    protected $logger;
    protected $sender;

    /**
     * @var \Linkmobility\Notifications\Observer\ConfigInterface
     */
    private $config;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        ConfigInterface $config,
        \Linkmobility\Notifications\Model\Api\Sms\Send $sender
    ) {
        $this->logger = $logger;
        $this->sender = $sender;
        $this->config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $payment = $event->getPayment();
        $order = $payment->getOrder();
        $address = ($order->getShippingAddress() ? : $order->getBillingAddress());
        $telephone = ($address ? $address->getTelephone() : null);

        $this->sender
            ->setSource(
                $this->config->getSourceNumber()
            )
            ->setDestination($telephone)
            ->setUserData(
                $this->config->getOrderRefundedTemplate()
            );
        $this->logger->info('Linkmobility: preparing request');
        try {
            $response = $this->sender->execute();
            $this->logger->info('Linkmobility: response received');
            $this->logger->info(print_r($response, true));
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }

        return $this;
    }
}