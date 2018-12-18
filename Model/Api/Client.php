<?php
namespace Linkmobility\Notifications\Model\Api;

use Linkmobility\Notifications\Api\ConfigInterface;

abstract class Client {

    const BASE_URI = "https://wsx.sp247.net/sms/";
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";

    protected $service;
    protected $method;
    protected $verb;
    protected $auth;
    protected $body;
    protected $head;
    protected $queryString;
    protected $_logger;

    /**
     * @var \Linkmobility\Notifications\Api\ConfigInterface
     */
    private $config;

    public function __construct (
        \Psr\Log\LoggerInterface $logger,
        ConfigInterface $config
    ){
        $this->_logger = $logger;
        $this->verb = self::METHOD_POST;
        $this->config = $config;
    }

    public function execute (array $requestBody = []){
        if ($this->method != NULL) {
            $service = $this->getService();
            $request = [];
            $this->setBody($requestBody);
            $this->setHead();
            $this->setAuth();
            if ($this->head){
                $request = array_merge($request, ["headers" => $this->head]);
            }
            if ($this->auth){
                $request = array_merge($request, $this->auth);
            }
            if ($this->body){
                $request = array_merge($request, ["body" => json_encode ($this->body)]);
            }
            if ($this->isEnabled()) {
                $this->_logger->info(print_r($request, TRUE));
                $response = $service->request($this->verb, $this->method, $request);
                return $response;
            } else {
                throw new \Exception("Linkmobility Client Exception: module is not enabled.");
            }
        } else {
            throw new \Exception("Linkmobility Client Exception: no method defined.");
        }
    }

    public function getEndpoint (){
        return $this->getURI() . $this->method;
    }

    public function setService (){
        if ($this->service == NULL) {
            $service = new \GuzzleHttp\Client(["base_uri" => $this->getURI()]);
            $this->service = $service;
        }
    }

    public function setMethod ($method){
        $this->method = $method;
    }

    public function setVerb ($verb){
        $this->verb = $verb;
    }

    public function getService(){
        if ($this->service == NULL){
            $this->setService();
        }
        return $this->service;
    }

    public function setBody (array $body = []){
        $platformId = $this->config->getPlatformId();
        $platformPartnerId = $this->config->getPlatformPartnerId();
        //$gateId = $this->config->getGateId();

        $this->body = array_merge($body, ["platformId" => $platformId, "platformPartnerId" => $platformPartnerId]);
    }

    public function setHead (array $headers = []){
        $this->head = array_merge($headers, ["Accept" => "application/json", "content-type" => "application/json"]);
    }

    protected function getURI (){
        return self::BASE_URI;
    }

    protected function setAuth (){
        $username = $this->config->getApiUser();
        $password = $this->config->getApiPassword();
        if ($username && $password) {
            $this->auth = ["auth" => [$username, $password]];
        }
    }

    protected function isEnabled() {
        return $this->config->isEnabled();
    }
}