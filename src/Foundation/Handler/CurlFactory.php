<?php

namespace Rsudipodev\BridgingSatusehat\Foundation\Handler;

class CurlFactory
{
    const DEFAULT_CONTENT_TYPE = 'application/json';

    /**
     * Make an HTTP request using cURL.
     *
     * @param string $endpoint   The URL to send the request to.
     * @param string $method     The HTTP method (GET, POST, PUT, DELETE).
     * @param mixed  $payload    The data to send in the request body.
     * @param string $header     Additional header information.
     * @param string $contentType The content type of the request.
     *
     * @return mixed The response from the server.
     *
     * @throws \RuntimeException If there is an error during the request.
     */
    public function request($endpoint, $method = "POST", $payload = "", $header = null, $contentType = self::DEFAULT_CONTENT_TYPE)
    {
        try {
            // Set default headers
            $defaultHeaders = [
                'Connection: close',
                'Content-Type: ' . $contentType,
            ];

            // Override headers based on method and additional header
            if (!empty($header)) {
                $defaultHeaders[] = 'Authorization: Bearer ' . $header;
            }

            // Set request type
            $isPost = strtoupper($method) === "POST";
            $isGet = strtoupper($method) === "GET";
            $isPut = strtoupper($method) === "PUT";
            $isDelete = strtoupper($method) === "DELETE";

            // Set cURL options
            $curlOptions = [
                CURLOPT_VERBOSE => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $defaultHeaders,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TCP_KEEPALIVE => 1,
                CURLOPT_TCP_KEEPIDLE => 60,
                CURLOPT_TCP_NODELAY => 1,
                CURLOPT_ENCODING => 'gzip, deflate, br',
            ];

            // Initialize cURL session
            $ch = curl_init();
            curl_setopt_array($ch, $curlOptions);
            curl_setopt($ch, CURLOPT_URL, $endpoint);

            // Execute cURL session
            $result = curl_exec($ch);
            $info = curl_getinfo($ch);
            $error = curl_error($ch);
            // dd($result);
            // Check for cURL errors
            if ($error) {
                throw new \RuntimeException('cURL error: ' . $error);
            }

            // // Check HTTP status code
            // $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // if ($httpStatusCode >= 400) {
            //     throw new \RuntimeException('HTTP error: ' . $httpStatusCode);
            // }

            // Close cURL session
            curl_close($ch);

            return $result;
        } catch (\Throwable $th) {
            throw new \RuntimeException($th->getMessage());
        }
    }
}
