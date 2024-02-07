<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\FHIR;

use Rsudipodev\BridgingSatusehat\Utility\Enpoint;
use Rsudipodev\BridgingSatusehat\Bridge\BridgeSatusehat;
use Rsudipodev\BridgingSatusehat\Bridge\Response\Practitioner as ResponsePractitioner;

class Practitioner
{
    protected $bridgeSatusehat;

    public function __construct(BridgeSatusehat $bridgeSatusehat)
    {
        $this->bridgeSatusehat = $bridgeSatusehat;
    }

    public function getNik($nik)
    {
        $respons = new ResponsePractitioner;
        $endpoint = Enpoint::practitionerUrl($nik);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respons->getNik($response);
    }

    public function getId($id)
    {
        $respons = new ResponsePractitioner;
        $endpoint = Enpoint::showPractitionerIdUrl($id);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respons->getId($response);
    }
}
