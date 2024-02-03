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
    $organization->addIdentifier('RAJAL'); //Wajib
    $organization->setActive(false); // Opsional
    $organization->setName('Unit Rawat Jalan'); //wajib
    $organization->addTelecom('email', 'sip'); //opsional
    $organization->addTelecom('phone', 'ab'); //opsional
    $organization->addTelecom('url', 'https://rsdiponegoroduasatu.com'); //opsional

    //Opsional
    $organization->addAddress([
        'line' => 'Jl. Diponegoro No. 21',
        'city' => [
            'name' => 'Klaten',
            'code' => '08954'
        ],
        'country' => 'Indonesia',
        'district' => 'Klaten Tengah',
        'postalCode' => '57412',
        'province' => 'Jawa Tengah',
        'village' => 'Klaten Tengah',
        'postalCode' => '12'
    ]);

    //Opsional
    $organization->setPartOf([
        'reference' => '018d53db-a178-71de-a8ca-666149009923',
        'display' => 'RSUD Dr. Soeroto Klaten'
    ]);

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
