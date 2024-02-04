<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\Response;

class Location
{
    public function convert($response): array
    {
        $data = json_decode($response, true);
        if ($data['status'] == false) {
            return Error::response($response);
        }
        // {
        //     "address": {
        //         "city": "Kab. Klaten",
        //         "country": "ID",
        //         "extension": [
        //             {
        //                 "extension": [
        //                     {
        //                         "url": "province",
        //                         "valueCode": "33"
        //                     },
        //                     {
        //                         "url": "city",
        //                         "valueCode": "3310"
        //                     },
        //                     {
        //                         "url": "district",
        //                         "valueCode": "331024"
        //                     },
        //                     {
        //                         "url": "village",
        //                         "valueCode": "3310242003"
        //                     },
        //                     {
        //                         "url": "rt",
        //                         "valueCode": "001"
        //                     },
        //                     {
        //                         "url": "rw",
        //                         "valueCode": "001"
        //                     }
        //                 ],
        //                 "url": "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode"
        //             }
        //         ],
        //         "line": [
        //             "Jl. Diponegoro No. 21"
        //         ],
        //         "postalCode": "57438",
        //         "use": "work"
        //     },
        //     "description": "Poli Anak - RS Umum Diponegoro Dua Satu Klaten",
        //     "id": "6956f1c6-e7ce-4174-bfd5-3ebc0b4da7e2",
        //     "identifier": [
        //         {
        //             "system": "http://sys-ids.kemkes.go.id/location/168777d6-5547-4883-a850-5c35605adcbe",
        //             "value": "ANA"
        //         }
        //     ],
        //     "managingOrganization": {
        //         "reference": "Organization/75738fc0-6677-4fdb-8560-8353ea1d0638"
        //     },
        //     "meta": {
        //         "lastUpdated": "2024-02-04T15:49:22.553985+00:00",
        //         "versionId": "MTcwNzA2MTc2MjU1Mzk4NTAwMA"
        //     },
        //     "mode": "instance",
        //     "name": "Poli Anak",
        //     "physicalType": {
        //         "coding": [
        //             {
        //                 "code": "ro",
        //                 "display": "Room",
        //                 "system": "http://terminology.hl7.org/CodeSystem/location-physical-type"
        //             }
        //         ]
        //     },
        //     "position": {
        //         "altitude": 0,
        //         "latitude": -7.7014433,
        //         "longitude": 110.6148779
        //     },
        //     "resourceType": "Location",
        //     "status": "active",
        //     "telecom": [
        //         {
        //             "system": "email",
        //             "use": "work",
        //             "value": "cahya@mail.com"
        //         },
        //         {
        //             "system": "phone",
        //             "use": "work",
        //             "value": "0895422611029"
        //         },
        //         {
        //             "system": "url",
        //             "use": "work",
        //             "value": "https://rsdiponegoroduasatu.com"
        //         }
        //     ]
        // }
        if ($data['resourceType'] !== 'Location') {
            return Error::checkOperationOutcome($data['resourceType'], $data);
        }

        $locationData = [
            'active'      => $data['active'],
            'ihs_number'  => $data['id'],
            'identifier'  => $data['identifier'][0]['value'],
            'name'        => $data['name'],
            'description' => $data['description'],
            'type'        => $data['physicalType']['coding'][0]['display'], // 'Room', 'Ward', 'Corridor', 'Entrance', 'Emergency Exit', 'Elevator', 'Staircase', 'Waiting Area', 'Parking Area', 'Toilet', 'Cafeteria', 'Pharmacy', 'Laboratory', 'Radiology', 'Operating Room', 'Delivery Room', 'Intensive Care Unit', 'Nursing Unit', 'Treatment Room', 'Consultation Room', 'Examination Room', 'Procedure Room', 'Recovery Room', 'Mortuary', 'Other'
            'code_type' => $data['physicalType']['coding'][0]['code'], // 'ro', 'wa', 'co', 'en', 'ee', 'el', 'st', 'wa', 'pa', 'to', 'ca', 'ph', 'la', 'ra', 'op', 'de', 'ic', 'nu', 'tr', 'co', 'ex', 'pr', 're', 'mo', 'ot'
            'address'     => [
                'city'       => $data['address'][0]['city'],
                'country'    => $data['address'][0]['country'],
                'line'       => $data['address'][0]['line'][0],
                'postalCode' => $data['address'][0]['postalCode'],
                'extension'  => [],
            ],
            'position'    => [
                'latitude'  => $data['position']['latitude'],
                'longitude' => $data['position']['longitude'],
                'altitude'  => $data['position']['altitude']
            ],
            'contact'     => [],
            'managingOrganization' => $data['managingOrganization']['reference'],
            'last_update' => $data['meta']['lastUpdated'],
        ];

        foreach ($data['telecom'] as $telecomItem) {
            $locationData['contact'][] = [
                'system' => $telecomItem['system'],
                'value'  => $telecomItem['value'],
                'use'    => $telecomItem['use']
            ];
        }

        foreach ($data['address'][0]['extension'][0]['extension'] as $extensionItem) {
            $locationData['address']['extension'][] = [
                'url'       => $extensionItem['url'],
                'valueCode' => $extensionItem['valueCode']
            ];
        }

        return [
            'status'   => true,
            'message'  => 'success',
            'response' => $locationData
        ];
    }
}
