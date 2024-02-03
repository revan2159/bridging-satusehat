<?php

namespace Rsudipodev\BridgingSatusehat\Foundation\Http;

use Rsudipodev\BridgingSatusehat\Foundation\Handler\CurlFactory;

class OAuth2Client
{
    protected $bridge;
    protected $accessToken;

    /**
     * OAuth2Client constructor.
     *
     * @param string $endpoint   The URL to obtain the access token.
     * @param array  $dataClient The client data for authentication.
     */
    public function __construct($endpoint, $dataClient)
    {
        $this->bridge = new CurlFactory();

        // try {
        $response = $this->bridge->request($endpoint, "POST", $dataClient, null, "application/x-www-form-urlencoded");
        $result = json_decode($response, true);

        if (isset($result['access_token'])) {
            $this->accessToken = $result['access_token'];
        } else {
            throw new \RuntimeException('Access token not found in the response.');
        }
        // } catch (\Throwable $th) {
        //     // Handle exceptions (e.g., log, rethrow, or take appropriate action)
        //     throw new \RuntimeException('Error obtaining access token: ' . $th->getMessage());
        // }
    }

    /**
     * Get the obtained access token.
     *
     * @return string|null The access token or null if not obtained.
     */
    public function setToken()
    {
        return $this->accessToken;
    }
}
