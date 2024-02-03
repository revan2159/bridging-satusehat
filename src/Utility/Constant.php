<?php

namespace Rsudipodev\BridgingSatusehat\Utility;

use Rsudipodev\BridgingSatusehat\Utility\Enviroment;

class Constant
{
    public static function getAuthUrl(): string
    {
        return Enviroment::authUrl();
    }

    public static function getBaseUrl(): string
    {
        return Enviroment::baseUrl();
    }

    public static function getConsentUrl(): string
    {
        return Enviroment::consentUrl();
    }

    public static function getKfaUrl(): string
    {
        return Enviroment::kfaUrl();
    }

    public static function getKycUrl(): string
    {
        return Enviroment::kycUrl();
    }
}
