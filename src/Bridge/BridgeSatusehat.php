<?php

namespace Rsudipodev\BridgingSatusehat\Bridge;

use Rsudipodev\BridgingSatusehat\Foundation\Http\OAuth2Client;
use Rsudipodev\BridgingSatusehat\Foundation\Handler\CurlFactory;
use Rsudipodev\BridgingSatusehat\Foundation\Http\ConfigSatusehat;

class BridgeSatusehat extends CurlFactory
{
    protected $auth;
    protected $access_token;
    protected $config;
    protected $endpointAuth = "/accesstoken?grant_type=client_credentials";

    public function __construct()
    {
        $this->config = new ConfigSatusehat;
        $this->auth = new OAuth2Client($this->config->setUrlAuth() . $this->endpointAuth, $this->config->setCredentials());
        $this->access_token = $this->auth->setToken();
    }

    public function getRequest($endpoint)
    {
        $respon = $this->makeRequest($endpoint, "GET");
        return $respon;
    }

    public function postRequest($endpoint, $data)
    {
        return $this->makeRequest($endpoint, "POST", $data);
    }

    public function putRequest($endpoint, $data)
    {
        return $this->makeRequest($endpoint, "PUT", $data);
    }
    // for text/plain
    public function textRequest($endpoint, $data)
    {
        return $this->makeRequest($endpoint, "POST", $data, "text/plain");
    }

    protected function makeRequest($endpoint, $method = "POST", $payload = "")
    {
        try {
            $result = $this->request($endpoint, $method, $payload, $this->access_token);
            return $result;
        } catch (\Throwable $th) {
            throw new \RuntimeException('Satusehat Error: ' . $th->getMessage());
        }
    }
}
