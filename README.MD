## Quick Start

Create or Update Organization

```php

use Rsudipodev\BridgingSatusehat\Bridge\FHIR\Organization;

protected $organization;

public function __construct(Organization $organization)
{
    $this->organization = $organization;
}

public function create()
{
    $organization = $this->organization;
    $organization->addIdentifier('Unit Keperawatan'); //Wajib
    $organization->setActive(false); // Opsional
    $organization->setName('Keperawatan RSU Diponegoro Klaten'); //wajib
    // Opsional
    $organization->addTelecom([
            'email' => 'rskbdiponegoro0@gmail.com',
            'phone' => '0895422611029',
            'url' => 'https://rsdiponegoroduasatu.com'
        ], 'work');

    //Opsional
    $organization->addAddress([
        'line' => 'Jl. Diponegoro No. 21',
        'country' => 'ID',
        'city' => [
            'name' => 'Kab. Klaten',
            'code' => '3310'
        ],
        'province' => '33',
        'district' => '331024',
        'village' => '3310242003',
        'postalCode' => '57438'
    ]);

    //Opsional
    $organization->setPartOf([
        'reference' => '018d53db-a178-71de-a8ca-666149009923',
        'display' => 'RSUD Dr. Soeroto Klaten'
    ]);

    // Chack Json Data Sebelum Kirim
    $organization = $organization->json();

    // For Create
    $organization = $organization->create();

    // For Update
    $organization = $organization->update('06829c7c-5b5e-4f45-9a5b-c5b0d641f4a8');

    //Json return
    return $organization;
}
```

Organization by Name

```php
    $organization = $this->organization;
    $cari = $organization->getName('Putut'); //Json return
```

Organization by ID

```php
    $organization = $this->organization;
    $cari = $organization->getId('06829c7c-5b5e-4f45-9a5b-c5b0d641f4a8'); //Json return
```

Organization by PartOf

```php
    $organization = $this->organization;
    $cari = $organization->getPartOf('06829c7c-5b5e-4f45-9a5b-c5b0d641f4a8'); //Json return
```
