<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\FHIR;

use Rsudipodev\BridgingSatusehat\Utility\Enpoint;
use Rsudipodev\BridgingSatusehat\Bridge\BridgeSatusehat;
use Rsudipodev\BridgingSatusehat\Bridge\Response\Patient as ResponsePatient;

class Patient
{
    protected $bridgeSatusehat;

    public function __construct(BridgeSatusehat $bridgeSatusehat)
    {
        $this->bridgeSatusehat = $bridgeSatusehat;
    }

    private $Patient = [
        "resourceType" => "Patient",
        "meta" => [
            "profile" => [
                "https://fhir.kemkes.go.id/r4/StructureDefinition/Patient"
            ]
        ],
    ];

    /**
     * Untuk menambahkan status aktif pasien
     * @param boolean $active status aktif pasien (true, false)
     */

    public function setActive($active = null)
    {
        $active =  is_bool($active) ? $active : true;
        $this->Patient['active'] = $active;
    }

    /**
     * Untuk menambahkan status kematian pasien
     * @param boolean $deceased status kematian pasien (true, false)
     */

    public function setDeceasedBoolean($deceased = null)
    {
        $deceased =  is_bool($deceased) ? $deceased : false;
        $this->Patient['deceasedBoolean'] = $deceased;
    }

    /** 
     * Untuk menambahkan status perkawinan (sipil) terakhir pasien.
     * @param string $maritalStatus status perkawinan pasien Default "M"
     * @example $maritalStatus = "M" | "S" | "D" | "W"
     * "A" => "Annulled", // Kontrak pernikahan dinyatakan batal dan tidak pernah ada
     * "D" => "Divorced", // Cerai
     * "I" => "Interlocutory", // Pernikahan yang belum selesai
     * "L" => "Legally Separated", // Pernikahan yang dihentikan secara hukum
     * "M" => "Married", // Menikah
     * "C" => "Common Law", // Pernikahan yang tidak diakui secara hukum
     * "P" => "Polygamous", // Pernikahan dengan lebih dari satu pasangan
     * "T" => "Domestic partner", // Pasangan hidup
     * "U" => "Unmarried", // Belum Menikah
     * "W" => "Widowed", // Duda / Janda
     * "S" => "Never Married", // Belum pernah menikah
     * "UNK" => "Unknown", // Tidak diketahui
     */

    public function setMaritalStatus(string $maritalStatus = null)
    {
        $maritalStatus =  is_string($maritalStatus) ? $maritalStatus : "M";
        $display = [

            "A" => "Annulled", // Kontrak pernikahan dinyatakan batal dan tidak pernah ada
            "D" => "Divorced", // Cerai
            "I" => "Interlocutory", // Pernikahan yang belum selesai
            "L" => "Legally Separated", // Pernikahan yang dihentikan secara hukum
            "M" => "Married", // Menikah
            "C" => "Common Law", // Pernikahan yang tidak diakui secara hukum
            "P" => "Polygamous", // Pernikahan dengan lebih dari satu pasangan
            "T" => "Domestic partner", // Pasangan hidup
            "U" => "Unmarried", // Belum Menikah
            "W" => "Widowed", // Duda / Janda
            "S" => "Never Married", // Belum pernah menikah
            "UNK" => "Unknown", // Tidak diketahui
        ];

        $this->Patient['maritalStatus'] = [
            "coding" => [
                [
                    "system" => "http://terminology.hl7.org/CodeSystem/v3-MaritalStatus",
                    "code" => $maritalStatus,
                    "display" => $display[$maritalStatus]
                ]
            ],
            "text" => $display[$maritalStatus]
        ];
    }

    /**
     * Untuk menambahkan identifier pasien
     * @param array $identifier array identifier pasien
     * @example $identifier = ['nik' => '1234567890', 'paspor' => '1234567890', 'kk' => '1234567890']
     */


    public function addIdentifier(array $identifier)
    {
        $this->Patient['identifier'] = [];

        foreach ($identifier as $key => $value) {
            $system = "https://fhir.kemkes.go.id/id/$key";
            $this->Patient['identifier'][] = [
                "use" => "official",
                "system" => $system,
                "value" => $value
            ];
        }
    }

    /**
     * Untuk menambahkan nama pasien
     * @param string $name nama pasien
     */

    public function setName(string $name)
    {
        $this->Patient['name'] = [
            [
                "use" => "official",
                "text" => $name
            ]
        ];
    }

    /**
     * Untuk menambahkan jenis kelamin pasien
     * @param string $gender jenis kelamin pasien
     * @example $gender = "female" | "male"
     */

    public function setGender(string $gender)
    {
        $this->Patient['gender'] = $gender;
    }

    /**
     * Untuk menambahkan tanggal lahir pasien
     * @param string $birthDate tanggal lahir pasien
     * @example $birthDate = "1990-01-01"
     */

    public function setBirthDate(string $birthDate)
    {
        $formatbirthDate = date('Y-m-d', strtotime($birthDate));
        $this->Patient['birthDate'] = $formatbirthDate;
    }

    /**
     * Untuk menambahkan Informasi Kelahiran Ganda untuk pasien Bayi baru lahir.
     * @param boolean $multipleBirthBoolean status kelahiran ganda / kembar
     * @example $multipleBirthBoolean = true // Untuk kasus kelahiran kembar
     */

    public function setMultipleBirthBoolean($multipleBirthBoolean)
    {
        $this->Patient['multipleBirthBoolean'] = is_bool($multipleBirthBoolean);
    }

    /**
     * Untuk menambahkan informasi urutan kelahiran untuk pasien Bayi baru lahir.
     * @param int $multipleBirthInteger urutan kelahiran
     * @example $multipleBirthInteger = 0 // Untuk kasus kelahiran tidak kembar dapat memasukkan nilai
     * @example $multipleBirthInteger = 1 // urutan kelahiran pertama
     * @example $multipleBirthInteger = 2 // urutan kelahiran kedua
     * @example $multipleBirthInteger = 3 // urutan kelahiran ketiga
     */

    public function setMultipleBirthInteger(int $multipleBirthInteger)
    {
        $this->Patient['multipleBirthInteger'] = $multipleBirthInteger;
    }

    /**
     * Untuk menambahkan alamat pasien
     * @param array $value array alamat pasien
     */
    public function addAddress(array $value = [])
    {
        $this->Patient['address'][] = [
            "use" => "home",
            "line" => [
                $value['line']
            ],
            "city" => $value['city']['name'],
            "postalCode" => $value['postalCode'],
            "country" => $value['country'] ?? "ID",
            "extension" => [
                [
                    "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                    "extension" => [
                        [
                            "url" => "province",
                            "valueCode" => $value['province']
                        ],
                        [
                            "url" => "city",
                            "valueCode" => $value['city']['code']
                        ],
                        [
                            "url" => "district",
                            "valueCode" => $value['district']
                        ],
                        [
                            "url" => "village",
                            "valueCode" => $value['village']
                        ],
                        [
                            "url" => "rt",
                            "valueCode" => $value['rt']
                        ],
                        [
                            "url" => "rw",
                            "valueCode" => $value['rw']
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Untuk menambahkan kontak pasien
     * @param array $value array kontak pasien
     */

    public function addContact(array $contacts)
    {
        $this->Patient['contact'] = [];

        foreach ($contacts as $contactDetails) {
            $telecoms = [];

            if (isset($contactDetails['telecoms']) && is_array($contactDetails['telecoms'])) {
                foreach ($contactDetails['telecoms'] as $telecom) {
                    $telecoms[] = [
                        "system" => $telecom['system'],
                        "value" => $telecom['value'],
                        "use" => $telecom['use']
                    ];
                }
            }

            $contact = [
                "relationship" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/v2-0131",
                                "code" => $contactDetails['relationshipCode']
                            ]
                        ]
                    ]
                ],
                "name" => [
                    "use" => "official",
                    "text" => $contactDetails['name']
                ],
                "telecom" => $telecoms
            ];

            $this->Patient['contact'][] = $contact;
        }
    }

    /**
     * Untuk menambahkan nomor telepon pasien
     * @param array $Telecom array nomor telepon pasien
     * @example $Telecom = ['phone' => '0895422611029', 'email' => 'cahya@mail.com']
     * @param string $use tipe kontak (work, home, temp, old, mobile)
     */

    public function addTelecom(array $telecom, $use)
    {
        foreach ($telecom as $key => $value) {
            $this->Patient['telecom'][] = [
                "system" => $key,
                "value" => $value,
                "use" => $use
            ];
        }
    }

    /**
     * Untuk menambahkan bahasa komunikasi pasien
     * @param string $language kode bahasa komunikasi pasien
     * @example $language = "id-ID" | "en-US" | "zh-CN" | "ar-SA" | "ms-MY" | "en-SG"
     */

    public function setCommunication(string $language = null)
    {
        $language = $language ?? "id-ID";
        $display = [
            "id-ID" => "Indonesian",
            "en-US" => "English",
            "zh-CN" => "Chinese",
            "ar-SA" => "Arabic",
            "ms-MY" => "Malay",
            "en-SG" => "Singapore",
        ];

        $this->Patient['communication'] = [
            [
                "language" => [
                    "coding" => [
                        [
                            "system" => "urn:ietf:bcp:47",
                            "code" => $language,
                            "display" => $display[$language]
                        ]
                    ],
                    "text" => $display[$language]
                ],
                "preferred" => true
            ]
        ];
    }

    /**
     * Untuk menambahkan informasi tambahan pasien
     * @param array $extension array informasi tambahan pasien
     */

    public function addExtension(array $extension)
    {
        $this->Patient['extension'] = [
            [
                "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/birthPlace",
                "valueAddress" => [
                    "city" => $extension['birthPlace']['city'],
                    "country" => $extension['birthPlace']['country'],
                ]
            ],
            [
                "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/citizenshipStatus",
                "valueCode" => $extension['citizenshipStatus']
            ]
        ];
    }

    // json build
    public function json(): string
    {

        // identifier wajib
        if (!array_key_exists('identifier', $this->Patient)) {
            return throw new \RuntimeException('Identifier pasien wajib diisi, gunekan patient->addIdentifier()');
        }

        // name wajib
        if (!array_key_exists('name', $this->Patient)) {
            return throw new \RuntimeException('Nama pasien wajib diisi, gunakan patient->setName()');
        }

        // birthDate wajib
        if (!array_key_exists('birthDate', $this->Patient)) {
            return throw new \RuntimeException('Tanggal lahir pasien wajib diisi gunakan patient->setBirthDate()');
        }

        // gender wajib
        if (!array_key_exists('gender', $this->Patient)) {
            return throw new \RuntimeException('Jenis kelamin pasien wajib diisi gunakan patient->setGender()');
        }

        // address wajib
        if (!array_key_exists('address', $this->Patient)) {
            return throw new \RuntimeException('Alamat pasien wajib diisi gunakan patient->addAddress()');
        }

        // multipleBirthInteger wajib
        // if (array_key_exists('multipleBirthBoolean', $this->Patient) && $this->Patient['multipleBirthBoolean'] == true) {
        //     if (!array_key_exists('multipleBirthInteger', $this->Patient)) {
        //         return throw new \RuntimeException('Urutan kelahiran pasien wajib diisi gunakan patient->setMultipleBirthInteger()');
        //     }
        // }
        if (!array_key_exists('multipleBirthInteger', $this->Patient)) {
            $this->setMultipleBirthInteger(0);
        }

        if (!array_key_exists('contact', $this->Patient)) {
            $this->Patient['contact'] = [];
        }

        if (!array_key_exists('telecom', $this->Patient)) {
            $this->Patient['telecom'] = [];
        }

        if (!array_key_exists('communication', $this->Patient)) {
            $this->Patient['communication'] = [];
        }

        if (!array_key_exists('extension', $this->Patient)) {
            $this->Patient['extension'] = [];
        }

        if (!array_key_exists('active', $this->Patient)) {
            $this->setActive(false);
        }

        if (!array_key_exists('deceasedBoolean', $this->Patient)) {
            $this->setDeceasedBoolean(false);
        }

        if (!array_key_exists('maritalStatus', $this->Patient)) {
            $this->setMaritalStatus('S');
        }

        return json_encode($this->Patient, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Untuk membuat data pasien
     * @return array
     */
    public function create()
    {
        $respone = new ResponsePatient;
        $endpoint = Enpoint::createPatientUrl();
        $data = $this->json();
        $response = $this->bridgeSatusehat->postRequest($endpoint, $data);
        dd($response);
        // return $respone->convert($response);
    }

    /**
     * Untuk mengupdate data pasien
     * @param string $id id pasien
     * @return array
     */
    // public function update($id)
    // {
    //     $respone = new ResponsePatient;
    //     $datJson = json_decode($this->json(), true);
    //     $datJson['id'] = $id;
    //     $newdata = json_encode($datJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    //     $endpoint = Enpoint::updatePatientUrl($id);
    //     $response = $this->bridgeSatusehat->putRequest($endpoint, $newdata);
    //     return $respone->convert($response);
    // }
    // Patient by nik
    public function getNik($nik)
    {
        $respone = new ResponsePatient;
        $endpoint = Enpoint::getPatientByNikUrl($nik);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respone->getNik($response);
    }

    // Patient by nik ibu
    public function getNikIbu($nik)
    {
        $respone = new ResponsePatient;
        $endpoint = Enpoint::getPatientByNikIbuUrl($nik);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respone->getNikIbu($response);
    }

    // Patient by id
    public function getId($id)
    {
        $respone = new ResponsePatient;
        $endpoint = Enpoint::showPatientIdUrl($id);
        $response = $this->bridgeSatusehat->getRequest($endpoint);
        return $respone->getId($response);
    }
}
