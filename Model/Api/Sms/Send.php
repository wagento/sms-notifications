<?php
namespace Linkmobility\Notifications\Model\Api\Sms;

class Send extends \Linkmobility\Notifications\Model\Api\Client
{

    private const TON = 'MSISDN';

    private $source;
    private $destination;
    private $userData;
    private $useDeliveryReport = false;
    private $ignoreResponse = false;


    /**
     * @param array $request
     * @return mixed
     * @throws \Exception
     */
    public function execute(array $request = [])
    {
        if (!$this->getSource()) {
            throw new \Exception('Linkmobility API: no source number defined');
        }
        if (!$this->getDestination()) {
            throw new \Exception('Linkmobility API: no destination number defined');
        }
        if (!$this->getUserData()) {
            throw new \Exception('Linkmobility API: text message is empty');
        }
        $this->setMethod('send');
        $request = array_merge($request, [
            'source' => $this->getSource(),
            'sourceTON' => self::TON,
            'destination' => $this->getDestination(),
            'destinationTON' => self::TON,
            'userData' => $this->userData,
            'useDeliveryReport' => $this->useDeliveryReport,
            'ignoreResponse' => $this->ignoreResponse
        ]);

        return parent::execute($request);
    }

    /**
     * @return String
     */
    public function getSource() : string
    {
        return $this->source;
    }

    /**
     * @param String $source
     * @return Send
     */
    public function setSource($source) : Send
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return String
     */
    public function getDestination() : string
    {
        return $this->destination;
    }

    /**
     * @param String $destination
     * @return Send
     */
    public function setDestination($destination) : Send
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return String
     */
    public function getUserData() : string
    {
        return $this->userData;
    }

    /**
     * @param String $userData
     * @return Send
     */
    public function setUserData($userData) : Send
    {
        $this->userData = $userData;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseDeliveryReport() : bool
    {
        return $this->useDeliveryReport;
    }

    /**
     * @param bool $useDeliveryReport
     * @return Send
     */
    public function setUseDeliveryReport($useDeliveryReport) : Send
    {
        $this->useDeliveryReport = $useDeliveryReport;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIgnoreResponse() : bool
    {
        return $this->ignoreResponse;
    }

    /**
     * @param bool $ignoreResponse
     * @return Send
     */
    public function setIgnoreResponse($ignoreResponse) : Send
    {
        $this->ignoreResponse = $ignoreResponse;
        return $this;
    }


}