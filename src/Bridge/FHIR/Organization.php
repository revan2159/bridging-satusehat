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

        // $this->organization['address'] = [
        //     [
        //         "use" => "work",
        //         "type" => "both",
        //         "line" => [
        //             $this->addLine()
        //         ],
        //         "city" => $this->addCity()->city,
        //         "postalCode" => $this->addPostalCode('57438'),
        //         "country" => "ID",
        //         "extension" => [
        //             [
        //                 "url" => "http://hl7.org/fhir/StructureDefinition/geolocation",
        //                 "extension" => [
        //                     [
        //                         "url" => "province",
        //                         "valueCode" => $this->addProvince()
        //                     ],
        //                     [
        //                         "url" => "city",
        //                         "valueCode" => $this->addCity()->kode
        //                     ],
        //                     [
        //                         "url" => "district",
        //                         "valueCode" => $this->addDistrict()
        //                     ],
        //                     [
        //                         "url" => "village",
        //                         "valueCode" => $this->addVillage()
        //                     ]
        //                 ]
        //             ]
        //         ]
        //     ]
        // ];
    }

    // public function addLine($line = null)
    // {
    //     $line = $line ?? 'Jl. Diponegoro No. 21';
    //     return $line;
    // }

    // public function addProvince($kode = null)
    // {
    //     $kode = $kode ?? '33'; //jateng
    //     return $kode;
    // }

    // public function addCity($city = null, $kode = null)
    // {
    //     $city = $city ?? 'Klaten';
    //     $kode = $kode ?? '3310'; // kabupaten klaten
    //     return (object) [
    //         "city" => $city,
    //         "kode" => $kode
    //     ];
    // }

    // public function addDistrict($kode = null)
    // {
    //     $kode = $kode ?? '331024'; //kecamatan klaten utara
    //     return $kode;
    // }

    // public function addVillage($kode = null)
    // {
    //     $kode = $kode ?? '3310242003'; //kelurahan klaten utara
    //     return $kode;
    // }

    // public function addPostalCode($kode)
    // {
    //     return $kode;
    // }


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
            // $this->addLine('Jl. Diponegoro No. 21 Klaten');
            // $this->addProvince('33');
            // $this->addCity('Klaten', '3310');
            // $this->addDistrict('331024');
            // $this->addVillage('3310242003');
            // $this->addPostalCode('57438');
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
        // $respon = [
        //     "active" => true,
        //     "address" => [
        //         0 => [
        //             "city" => "Klaten",
        //             "country" => "ID",
        //             "extension" => [
        //                 0 => [
        //                     "extension" => [
        //                         0 => [
        //                             "url" => "province",
        //                             "valueCode" => "33"
        //                         ],
        //                         1 => [
        //                             "url" => "city",
        //                             "valueCode" => "3310"
        //                         ],
        //                         2 => [
        //                             "url" => "district",
        //                             "valueCode" => "331024"
        //                         ],
        //                         3 => [
        //                             "url" => "village",
        //                             "valueCode" => "3310242003"
        //                         ]
        //                     ],
        //                     "url" => "http://hl7.org/fhir/StructureDefinition/geolocation"
        //                 ]
        //             ],
        //             "line" => [
        //                 0 => "Jl. Diponegoro No. 21"
        //             ],
        //             "postalCode" => "57438",
        //             "type" => "both",
        //             "use" => "work"
        //         ]
        //     ],
        //     "id" => "06829c7c-5b5e-4f45-9a5b-c5b0d641f4a8",
        //     "identifier" => [
        //         0 => [
        //             "system" => "http://sys-ids.kemkes.go.id/organization/168777d6-5547-4883-a850-5c35605adcbe",
        //             "use" => "official",
        //             "value" => "RAJAL"
        //         ]
        //     ],
        //     "meta" => [
        //         "lastUpdated" => "2024-01-30T12:14:04.751396+00:00",
        //         "versionId" => "MTcwNjYxNjg0NDc1MTM5NjAwMA"
        //     ],
        //     "name" => "Rawat Jalan RSU Diponegoro Dua Satu Klaten",
        //     "partOf" => [
        //         "reference" => "Organization/168777d6-5547-4883-a850-5c35605adcbe"
        //     ],
        //     "resourceType" => "Organization",
        //     "telecom" => [
        //         0 => [
        //             "system" => "email",
        //             "use" => "work",
        //             "value" => "rskbdiponegoro0@gmail.com"
        //         ],
        //         1 => [
        //             "system" => "phone",
        //             "use" => "work",
        //             "value" => "0895422611029"
        //         ],
        //         2 => [
        //             "system" => "url",
        //             "use" => "work",
        //             "value" => "https://rsudiponegoro21klaten.com"
        //         ]
        //     ],
        //     "type" => [
        //         0 => [
        //             "coding" => [
        //                 0 => [
        //                     "code" => "dept",
        //                     "display" => "Hospital Department",
        //                     "system" => "http://terminology.hl7.org/CodeSystem/organization-type"
        //                 ]
        //             ]
        //         ]
        //     ]
        // ];
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

    // public function show($id = null)
    // {
    //     $id = $id ?? $this->organizationId;
    //     $endpoint = '/Organization/' . $id;
    //     $response = $this->bridgeSatusehat->getRequest($endpoint);
    //     return $this->show($response);
    // }

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
