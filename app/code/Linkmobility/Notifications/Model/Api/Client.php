<?php
namespace Linkmobility\Notifications\Model\Api;

class Client {

    const BASE_URI = "https://wsx.sp247.net/sms/";
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";

    protected $service;
    protected $method;
    protected $verb;
    protected $scopeConfig;
    protected $auth;
    protected $body;
    protected $head;
    protected $queryString;

    public function __construct (\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig){
        $this->scopeConfig = $scopeConfig;
        $this->verb = self::METHOD_POST;
    }

    public function execute (){
        if ($this->method != NULL) {
            $service = $this->getService();
            $request = array_merge($this->auth, $this->body);
            $response = $service->request($this->verb, $this->method, $request);

            return $response;
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

    protected function getURI (){
        return self::BASE_URI;
    }

    protected function setAuth (){
        $username = $this->scopeConfig->getValue("customer/linkmobility_notifications/username");
        $password = $this->scopeConfig->getValue("customer/linkmobility_notifications/password");
        if ($username && $password) {
            $this->auth = ["auth" => [$username, $password]];
        }
    }
}