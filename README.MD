## Quick Start

Create Organization

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
    $organization = $organization->create();
    //Json return
    return $organization;
}
```
