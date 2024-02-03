<?php

namespace Rsudipodev\BridgingSatusehat\Utility;


class Enviroment
{
    public static function authUrl(): string
    {
        $authUrl = getenv('SATUSEHAT_AUTH');

        if (!$authUrl) {
            throw new \RuntimeException("Satusehat Auth URL not found in Enviroment");
        }

        return $authUrl;
    }

    public static function baseUrl(): string
    {
        $baseUrl = getenv('SATUSEHAT_BASE');

        if (!$baseUrl) {
            throw new \RuntimeException("Satusehat Base URL not found in Enviroment");
        }

        return $baseUrl;
    }

    public static function consentUrl(): string
    {
        $consentUrl = getenv('SATUSEHAT_CONSENT');

        if (!$consentUrl) {
            throw new \RuntimeException("Satusehat Consent URL not found in Enviroment");
        }

        return $consentUrl;
    }

    public static function kfaUrl(): string
    {
        $kfaUrl = getenv('SATUSEHAT_KFA');

        if (!$kfaUrl) {
            throw new \RuntimeException("Satusehat KFA URL not found in Enviroment");
        }

        return $kfaUrl;
    }

    public static function kycUrl(): string
    {
        $kycUrl = getenv('SATUSEHAT_KYC');

        if (!$kycUrl) {
            throw new \RuntimeException("Satusehat KYC URL not found in Enviroment");
        }

        return $kycUrl;
    }


    public static function clientId(): string
    {
        $clientId = getenv('SATUSEHAT_CLIENTID');

        if (!$clientId) {
            throw new \RuntimeException("Satusehat Client ID not found in Enviroment");
        }

        return $clientId;
    }

    public static function clientSecret(): string
    {
        $clientSecret = getenv('SATUSEHAT_CLIENTSECRET');

        if (!$clientSecret) {
            throw new \RuntimeException("Satusehat Client Secret not found in Enviroment");
        }

        return $clientSecret;
    }

    public static function organizationId(): string
    {
        $organizationId = getenv('SATUSEHAT_ORGID');

        if (!$organizationId) {
            throw new \RuntimeException("Satusehat Organization ID not found in Enviroment");
        }

        return $organizationId;
    }
}
