<?php
namespace Linkmobility\Notifications\Model\Api\Sms;

class Send extends \Linkmobility\Notifications\Model\Api\Client {

    const TON = "MSISDN";

    private $source;
    private $destination;
    private $userData;
    private $useDeliveryReport = FALSE;
    private $ignoreResponse = TRUE;

    public function execute()
    {
        if (!$this->getSource()) {
            throw new \Exception("Linkmobility API: no source number defined");
        }
        if (!$this->getDestination()) {
            throw new \Exception("Linkmobility API: no destination number defined");
        }
        if (!$this->getUserData()) {
            throw new \Exception("Linkmobility API: text message is empty");
        }
        $request = [
            "source" => $this->getSource(),
            "sourceTON" => self::TON,
            "destination" => $this->getDestination(),
            "destinationTON" => self::TON,
            "userData" => $this->userData,
            "useDeliveryReport" => $this->useDeliveryReport,
            "ignoreResponse" => $this->ignoreResponse
        ];

        return parent::execute($request);
    }

    /**
     * @return String
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param String $source
     * @return Send
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return String
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param String $destination
     * @return Send
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return String
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     * @param String $userData
     * @return Send
     */
    public function setUserData($userData)
    {
        $this->userData = $userData;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseDeliveryReport()
    {
        return $this->useDeliveryReport;
    }

    /**
     * @param bool $useDeliveryReport
     * @return Send
     */
    public function setUseDeliveryReport($useDeliveryReport)
    {
        $this->useDeliveryReport = $useDeliveryReport;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIgnoreResponse()
    {
        return $this->ignoreResponse;
    }

    /**
     * @param bool $ignoreResponse
     * @return Send
     */
    public function setIgnoreResponse($ignoreResponse)
    {
        $this->ignoreResponse = $ignoreResponse;
        return $this;
    }


}