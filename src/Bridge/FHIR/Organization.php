<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\FHIR;

use Rsudipodev\BridgingSatusehat\Utility\Enpoint;
use Rsudipodev\BridgingSatusehat\Utility\StrHelper;
use Rsudipodev\BridgingSatusehat\Utility\Enviroment;
use Rsudipodev\BridgingSatusehat\Bridge\BridgeSatusehat;
use Rsudipodev\BridgingSatusehat\Bridge\Response\Organization as ResponseOrganization;

class Organization
{
    protected $organizationId;
    protected $bridgeSatusehat;
    protected $endpoint;

    public function __construct(BridgeSatusehat $bridgeSatusehat, Enpoint $endpoint)
    {
        $this->organizationId = Enviroment::organizationId();
        $this->endpoint = $endpoint;
        $this->bridgeSatusehat = $bridgeSatusehat;
    }

    private $organization =
    [
        "resourceType" => "Organization",
    ];

    /**
     * Untuk menambahkan status aktif organisasi
     * @param boolean $active status aktif organisasi (true, false)
     */

    public function setActive($active = null)
    {
        $active =  is_bool($active) ? $active : true;
        $this->organization['active'] = $active;
    }

    /**
     * Untuk menambahkan identifier organisasi
     * @param string $identifier kode identifier organisasi 
     */

    public function addIdentifier($identifier)
    {
        $this->organization['identifier'] = [[
            "use" => "official",
            "system" => "http://sys-ids.kemkes.go.id/organization/" . $this->organizationId,
            "value" => $identifier
        ]];
    }

    /**
     * Untuk menambahkan tipe organisasi
     * @param string $typekode kode tipe organisasi (dept, inst, team, etc)
     * @param string $typename nama tipe organisasi (Hospital Department, Institution, Team, etc)
     */

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

    /**

     * Untuk menambahkan nama organisasi
     * @param string $name nama organisasi 
     * 
     */

    public function setName($name): void
    {
        $this->organization['name'] = StrHelper::getName($name);
    }

    /**
     * Untuk menambahkan kontak organisasi
     * @param array $value array yang berisi email, phone, url
     * @param string $use tipe kontak (work, home, temp, old, mobile)
     */

    public function addTelecom(array $value = [], $use = 'work'): void
    {
        $defaultValues = [
            'email' => 'rskbdiponegoro0@gmail.com',
            'phone' => '0895422611029',
            'url'   => 'https://rsdiponegoroduasatu.com',
        ];

        foreach ($defaultValues as $key => $defaultValue) {
            $val = array_key_exists($key, $value) ? $value[$key] : $defaultValue;

            $this->organization['telecom'][] = [
                'system' => $key,
                'value'  => $val,
                'use'    => $use,
            ];
        }
    }

    /**
     * Untuk menambahkan alamat organisasi
     * @param array $value array yang berisi line, city, postalCode, country, province, city, district, village
     */

    public function addAddress(array $value = []): void
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

    /**
     * Untuk menambahkan bagian dari organisasi
     * @param array $setPartOf array yang berisi reference dan display
     */

    public function setPartOf(array $setPartOf = [])
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

    /**
     * Untuk mengubah data menjadi json 
     * Biasanya digunakan untuk mengecek data yang akan dikirim
     * @return string
     */

    public function json(): string
    {
        if (!array_key_exists('active', $this->organization)) {
            $this->setActive();
        }
        if (!array_key_exists('identifier', $this->organization)) {
            return 'Please use organization->addIdentifier(identifier_kode_name) to pass the data';
        }
        if (!array_key_exists('telecom', $this->organization)) {
            $this->addTelecom([]);
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

    /**
     * Untuk membuat data organisasi
     * @return array
     */
    public function create(): array
    {
        $respone = new ResponseOrganization;
        $endpoint = $this->endpoint->createOrganizationUrl();
        $data = $this->json();
        $response = $this->bridgeSatusehat->postRequest($endpoint, $data);
        return $respone->convert($response);
    }

    /**
     * Untuk mengupdate data organisasi
     * @param string $id id organisasi
     * @return array
     */

    public function update($id): array
    {
        $respone = new ResponseOrganization;
        $datJson = json_decode($this->json(), true);
        $datJson['id'] = $id;
        $newdata = json_encode($datJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $endpoint = $this->endpoint->updateOrganizationUrl($id);
        $response = $this->bridgeSatusehat->putRequest($endpoint, $newdata);
        return $respone->convert($response);
    }

    /**
     * Untuk mendapatkan data organisasi
     * @param string $uuid id organisasi
     * @return array
     */

    public function getPartOf($uuid = null): array
    {
        $respone = new ResponseOrganization;
        $uuid = $uuid ?? $this->organizationId;
        $endpoint = $this->endpoint->showOrganizationUrl($uuid);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respone->getPartOf($response);
    }

    /**
     * Untuk mendapatkan data organisasi berdasarkan nama
     * @param string $name nama organisasi
     * @return array
     */

    public function getName($name = null): array
    {
        $respone = new ResponseOrganization;
        $name = $name ?? 'Rumah Sakit Diponegoro';
        $endpoint = $this->endpoint->showOrganizationbyNameUrl($name);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respone->getName($response);
    }

    /**
     * Untuk mendapatkan data organisasi berdasarkan id
     * @param string $uuid id organisasi
     * @return array
     */

    public function getId($uuid = null): array
    {
        $respone = new ResponseOrganization;
        $uuid = $uuid ?? $this->organizationId;
        $endpoint = $this->endpoint->showOrganizationIdUrl($uuid);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respone->getId($response);
    }
}
