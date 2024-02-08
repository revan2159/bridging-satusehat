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

    public function setMaritalStatus($maritalStatus = null)
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
            "use" => "official",
            "text" => $name
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
        $this->Patient['address'] = [
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

    private $contak = [
        "type" => "C"

    ];


    public function addContact($contac)
    {
        $this->Patient['contact'] = [];

        foreach ($contac as $key => $value) {
            $this->Patient['contact'][] = [
                "relationship" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/v2-0131",
                                "code" => $key
                            ]
                        ]
                    ]
                ],
                "name" => [
                    "use" => "official",
                    "text" => $value['name']
                ],
                "telecom" => []
            ];
        }

        foreach ($contac as $key => $value) {
            if (array_key_exists('phone', $value)) {
                $this->Patient['contact'][$key]['telecom'][] = [
                    "system" => "phone",
                    "value" => $value['phone'],
                    "use" => $value['use']
                ];
            }
            if (array_key_exists('email', $value)) {
                $this->Patient['contact'][$key]['telecom'][] = [
                    "system" => "email",
                    "value" => $value['email'],
                    "use" => $value['use']
                ];
            }
        }
    }


    /**
     * Untuk menambahkan nomor telepon pasien
     * @param string $system jenis nomor telepon pasien
     * @param string $value nomor telepon pasien
     * @param string $use penggunaan nomor telepon pasien
     * @example $system = "phone" | "fax" | "email" | "pager" | "url" | "sms" | "other"
     * @example $use = "home" | "work" | "temp" | "old" | "mobile"
     */

    public function addTelecom(string $system, string $value, string $use = null)
    {
        $use = $use ?? 'home';
        $this->Patient['telecom'] = [
            [
                "system" => $system,
                "value" => $value,
                "use" => $use
            ]
        ];
    }
}
