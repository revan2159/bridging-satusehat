<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\Response;

class Error
{
    public static function response($response)
    {
        $message = json_encode($response);
        return [
            'status' => false,
            'message' => $message
        ];
    }

    public static function getToken($getToken)
    {
        return [
            'status' => false,
            'message' => $getToken['message']
        ];
    }

    public static function http($http)
    {
        return [
            'status' => false,
            'message' => $http['message']
        ];
    }

    public static function checkOperationOutcome($resType, $data)
    {
        if ($resType == 'OperationOutcome') {
            return [
                'status' => false,
                'error' => $data['issue'][0]['code'] ?? 'unknown_error',
                'message' => $data['issue'][0]['details']['text'] ?? 'Unknown error!'
            ];
        }
        return [
            'status' => false,
            'message' => 'Unknown error!'
        ];
    }

    public static function searchIsEmpty($response)
    {
        $message = json_encode($response);
        if (strpos($message, '\"total\":0') !== false) {
            return true;
        }
        return false;
    }
}
