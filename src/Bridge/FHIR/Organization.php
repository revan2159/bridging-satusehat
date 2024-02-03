<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\FHIR;

use Rsudipodev\BridgingSatusehat\Utility\Enviroment;
use Rsudipodev\BridgingSatusehat\Bridge\BridgeSatusehat;
use Rsudipodev\BridgingSatusehat\Bridge\Response\Organization as ResponseOrganization;
use Rsudipodev\BridgingSatusehat\Utility\Enpoint;

class Organization
{
    protected $organizationId;
    protected $bridgeSatusehat;
    protected $endpoint;

    public function __construct(BridgeSatusehat $bridgeSatusehat)
    {
        $this->organizationId = Enviroment::organizationId();
        $this->endpoint = new Enpoint;
        $this->bridgeSatusehat = $bridgeSatusehat;
    }

    private $organization =
    [
        "resourceType" => "Organization",
    ];

    public function setActive($active = null)
    {
        $active =  is_bool($active) ? $active : true;
        $this->organization['active'] = $active;
    }

    public function addIdentifier($identifier)
    {
        $this->organization['identifier'] = [[
            "use" => "official",
            "system" => "http://sys-ids.kemkes.go.id/organization/" . $this->organizationId,
            "value" => $identifier
        ]];
    }

    public function setType($typekode = null, $typename = null)
    {
        $typekode = $typekode ?? 'dept';
        $typename = $typename ?? 'Hospital Department';
        $this->organization['type'] = [
            [
                "coding" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/organization-type",
                        "code" => $typekode,
                        "display" => $typename
                    ]
                ]
            ]
        ];
    }

    public function setName($name)
    {
        $this->organization['name'] = $name;
    }

    public function addTelecom($key, $value, $use = null)
    {
        $use = $use ?? 'work';

        $this->organization['telecom'][] = [
            "system" => $key,
            "value" => $value,
            "use" => $use
        ];
    }

    public function addAddress(array $value = null): void
    {
        $this->organization['address'] = [
            [
                "use" => "work",
                "type" => "both",
                "line" => [
                    $value['line'] ?? 'Jl. Diponegoro No. 21'
                ],
                "city" => $value['city']['name'] ?? 'Klaten',
                "postalCode" => $value['postalCode'] ?? '57438',
                "country" => $value['country'] ?? "ID",
                "extension" => [
                    [
                        "url" => "http://hl7.org/fhir/StructureDefinition/geolocation",
                        "extension" => [
                            [
                                "url" => "province",
                                "valueCode" => $value['province'] ?? '33'
                            ],
                            [
                                "url" => "city",
                                "valueCode" => $value['city']['code'] ?? '3310'
                            ],
                            [
                                "url" => "district",
                                "valueCode" => $value['district'] ?? '331024'
                            ],
                            [
                                "url" => "village",
                                "valueCode" => $value['village'] ?? '3310242003'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function setPartOf($setPartOf)
    {
        $setPartid = $setPartOf['reference'] ?? null;
        $display = $setPartOf['display'] ?? null;
        $partOfid = $setPartid ?? $this->organizationId;
        $this->organization['partOf'] = [
            "reference" => "Organization/" . $partOfid,
        ];
        if ($display != null) {
            $this->organization['partOf']['display'] = $display;
        } else {
            unset($this->organization['partOf']['display']);
        }
    }

    public function json()
    {
        if (!array_key_exists('active', $this->organization)) {
            $this->setActive();
        }
        if (!array_key_exists('identifier', $this->organization)) {
            return 'Please use organization->addIdentifier(identifier_kode_name) to pass the data';
        }
        if (!array_key_exists('telecom', $this->organization)) {
            $this->addTelecom('email', 'rskbdiponegoro0@gmail.com');
            $this->addTelecom('phone', '0895422611029');
            $this->addTelecom('url', 'https://rsdiponegoroduasatu.com');
        }
        if (!array_key_exists('type', $this->organization)) {
            $this->setType();
        }

        if (!array_key_exists('name', $this->organization)) {
            return 'Please use organization->setName($organization_name) to pass the data';
        }
        if (!array_key_exists('address', $this->organization)) {
            $this->addAddress();
        }
        if (!array_key_exists('partOf', $this->organization)) {
            $this->setPartOf([]);
        }
        return json_encode($this->organization, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create()
    {
        $respone = new ResponseOrganization;
        $endpoint = $this->endpoint->createOrganizationUrl();
        $data = $this->json();
        $response = $this->bridgeSatusehat->postRequest($endpoint, $data);
        return $respone->convert($response);
    }

    public function update($id)
    {
        $respone = new ResponseOrganization;
        $datJson = json_decode($this->json(), true);
        $datJson['id'] = $id;
        $newdata = json_encode($datJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $endpoint = $this->endpoint->updateOrganizationUrl($id);
        $response = $this->bridgeSatusehat->putRequest($endpoint, $newdata);
        return $respone->convert($response);
    }

    public function getPartOf($uuid = null)
    {
        $respone = new ResponseOrganization;
        $uuid = $uuid ?? $this->organizationId;
        $endpoint = $this->endpoint->showOrganizationUrl($uuid);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respone->getPartOf($response);
    }

    public function getName($name = null)
    {
        $respone = new ResponseOrganization;
        $name = $name ?? $this->organizationId;
        $endpoint = $this->endpoint->showOrganizationbyNameUrl($name);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respone->getName($response);
    }

    public function getId($uuid = null)
    {
        $respone = new ResponseOrganization;
        $uuid = $uuid ?? $this->organizationId;
        $endpoint = $this->endpoint->showOrganizationIdUrl($uuid);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respone->getId($response);
    }
}
