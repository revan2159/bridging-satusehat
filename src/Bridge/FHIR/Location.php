<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\FHIR;

use Rsudipodev\BridgingSatusehat\Utility\Enpoint;
use Rsudipodev\BridgingSatusehat\Utility\StrHelper;
use Rsudipodev\BridgingSatusehat\Utility\Enviroment;
use Rsudipodev\BridgingSatusehat\Bridge\BridgeSatusehat;
use Rsudipodev\BridgingSatusehat\Bridge\Response\Location as ResponseLocation;

class Location
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

    private $location =
    [
        "resourceType" => "Location",
        "mode" => "instance",
    ];

    /**
     * Untuk menambahkan status aktif lokasi
     * @param string $active status aktif lokasi (inactive, suspended, active)
     * Definition
     * The status property covers the general availability of the resource, 
     * not the current value which may be covered by the operationStatus, 
     * or by a schedule/slots if they are configured for the location.
     */

    public function setActive(string $active = null)
    {
        switch ($active) {
            case 'inactive':
                $this->location['status'] = 'inactive';
                break;
            case 'suspended':
                $this->location['status'] = 'suspended';
                break;
            case 'active':
                $this->location['status'] = 'active';
                break;
            default:
                $this->location['status'] = 'active';
                break;
        }
    }

    /**
     * Untuk menambahkan identifier lokasi
     * @param string $identifier kode identifier lokasi 
     * Short description
     * Unique code or number identifying the location to its users
     * Definition 
     * Unique code or number identifying the location to its users.
     * Requirements 
     * Organization label locations in registries, need to keep track of those.
     */

    public function addIdentifier(string $identifier = null)
    {
        $this->location['identifier'] = [[
            "system" => "http://sys-ids.kemkes.go.id/location/" . $this->organizationId,
            "value" => $identifier
        ]];
    }

    /**
     * Untuk menambahkan nama lokasi
     * @param string $name nama lokasi
     * Short description
     * Name of the location as used by humans. Does not need to be unique.
     * Definition 
     * Name of the location as used by humans. Does not need to be unique.
     * Requirements 
     * Need to be able to identify locations by name.
     */

    public function setName(string $name = null)
    {
        $this->location['name'] = StrHelper::getName($name);
        $this->location['description'] = StrHelper::getName($name) . " - RS Umum Diponegoro Dua Satu Klaten";
    }

    /**
     * Untuk menambahkan tipe lokasi
     * @param string $typekode kode tipe lokasi (bu, wi, co, ro, ve, ho, ca, rd, area)
     * ro = Room | co = Corridor | ve = Vehicle | ho = House | ca = Cabinet | rd = Road | area = Area
     * Short description
     * Indicates the type of function performed at the location.
     * 
     * Definition
     * Indicates the type of function performed at the location.
     * 
     * Requirements
     * Need to be able to identify locations by type.
     */

    public function addPhysicalType(string $physical_type = null)
    {
        $code = $physical_type ? $physical_type : 'ro';
        $display = [
            'bu' => 'Building',
            'wi' => 'Wing',
            'co' => 'Corridor',
            'ro' => 'Room',
            've' => 'Vehicle',
            'ho' => 'House',
            'ca' => 'Cabinet',
            'rd' => 'Road',
            'area' => 'Area',
        ];
        $this->location['physicalType'] = [
            "coding" => [
                [
                    "system" => "http://terminology.hl7.org/CodeSystem/location-physical-type",
                    "code" => $code,
                    "display" => $display[$code]
                ]
            ]
        ];
    }

    /**
     * Untuk menambahkan alamat lokasi
     * @param array $address array yang berisi alamat lokasi
     * Short description
     * Physical location.
     * Definition
     * The absolute geographic location of the Location, expressed using the WGS84 datum (This is the same co-ordinate system used in KML).
     * Requirements
     * Need to be able to identify locations by location.
     */

    public function addAddress(array $value = [])
    {
        $this->location['address'] = [
            "use" => "work",
            "line" => [
                $value['line'] ?? 'Jl. Diponegoro No. 21'
            ],
            "city" => $value['city']['name'] ?? 'Klaten',
            "postalCode" => $value['postalCode'] ?? '57438',
            "country" => $value['country'] ?? "ID",
            "extension" => [
                [
                    "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
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
                        ],
                        [
                            "url" => "rt",
                            "valueCode" => $value['rt'] ?? '001'
                        ],
                        [
                            "url" => "rw",
                            "valueCode" => $value['rw'] ?? '001'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Untuk menambahkan koordinat lokasi
     * @param array $value array yang berisi koordinat lokasi
     * longitude = 110.6148779
     * latitude = -7.7014433
     * Short description
     * Physical location.
     * Definition
     * The absolute geographic location of the Location, expressed using the WGS84 datum (This is the same co-ordinate system used in KML).
     * Requirements
     * Need to be able to identify locations by location.
     */

    public function addPosition(array $value = [])
    {
        $this->location['position'] = [
            "longitude" => floatval($value['longitude'] ?? "110.6148779"),
            "latitude" => floatval($value['latitude'] ?? "-7.7014433"),
            "altitude" => 0
        ];
    }

    /**
     * Untuk menambahkan organisasi yang mengelola lokasi
     * @param array $managing_organization array yang berisi reference dan display
     * Short description
     * The organization that is responsible for the provisioning and upkeep of the location.
     * Definition
     * The organization that is responsible for the provisioning and upkeep of the location.
     * Requirements
     * Need to be able to identify the organization responsible for the location.
     */

    public function setManagingOrganization(array $managing_organization = [])
    {
        $organizationId = $managing_organization['reference'] ?? $this->organizationId;
        $display = $managing_organization['display'] ?? null;
        $this->location['managingOrganization'] = [
            "reference" => "Organization/" . $organizationId,
        ];
        if ($display !== null) {
            $this->location['managingOrganization']['display'] = $display;
        } else {
            unset($this->location['managingOrganization']['display']);
        }
    }

    /**
     * Untuk menambahkan data ke dalam array telecom
     * @param array $value array yang berisi system, value, use
     * Short description
     * Contact details of the location.
     * Definition
     * The contact details of communication devices available at the location. This can include phone numbers, fax numbers, mobile numbers, email addresses and web sites.
     * Requirements
     * Need to be able to contact the location.
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

            $this->location['telecom'][] = [
                'system' => $key,
                'value'  => $val,
                'use'    => $use,
            ];
        }
    }

    /**
     * Untuk mengubah data menjadi json 
     * Biasanya digunakan untuk mengecek data yang akan dikirim
     * @return string
     */

    public function json(): string
    {
        if (!array_key_exists('status', $this->location)) {
            $this->setActive();
        }
        if (!array_key_exists('identifier', $this->location)) {
            return 'Please use location->addIdentifier(identifier_kode_name) to pass the data';
        }
        if (!array_key_exists('telecom', $this->location)) {
            $this->addTelecom([]);
        }
        if (!array_key_exists('physicalType', $this->location)) {
            $this->addPhysicalType();
        }
        if (!array_key_exists('name', $this->location)) {
            return 'Please use location->setName($location_name) to pass the data';
        }
        if (!array_key_exists('address', $this->location)) {
            return 'Please use location->addAddress($location_address) to pass the data';
        }
        if (!array_key_exists('position', $this->location)) {
            $this->addPosition();
        }
        if (!array_key_exists('managingOrganization', $this->location)) {
            return 'Please use location->setManagingOrganization($managing_organization) to pass the data';
        }

        return json_encode($this->location, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Untuk membuat lokasi baru
     * @return array
     */

    public function create()
    {
        $respone = new ResponseLocation;
        $endpoint = $this->endpoint->createLocationUrl();
        $data = $this->json();
        $response = $this->bridgeSatusehat->postRequest($endpoint, $data);
        dd($response);
        return $respone->convert($response);
    }
}
