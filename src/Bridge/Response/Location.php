<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\Response;

class Location
{
    public function convert($response): array
    {
        $data = json_decode($response, true);
        if ($data['resourceType'] !== 'Location') {
            return Error::checkOperationOutcome($data['resourceType'], $data);
        }
        $locationData = [
            'status'      => $data['status'],
            'ihs_number'  => $data['id'],
            'identifier'  => $data['identifier'][0]['value'],
            'name'        => $data['name'],
            'description' => $data['description'],
            'type'        => $data['physicalType']['coding'][0]['display'], // 'Room', 'Ward', 'Corridor', 'Entrance', 'Emergency Exit', 'Elevator', 'Staircase', 'Waiting Area', 'Parking Area', 'Toilet', 'Cafeteria', 'Pharmacy', 'Laboratory', 'Radiology', 'Operating Room', 'Delivery Room', 'Intensive Care Unit', 'Nursing Unit', 'Treatment Room', 'Consultation Room', 'Examination Room', 'Procedure Room', 'Recovery Room', 'Mortuary', 'Other'
            'type_code' => $data['physicalType']['coding'][0]['code'], // 'ro', 'wa', 'co', 'en', 'ee', 'el', 'st', 'wa', 'pa', 'to', 'ca', 'ph', 'la', 'ra', 'op', 'de', 'ic', 'nu', 'tr', 'co', 'ex', 'pr', 're', 'mo', 'ot'
            'address'     => [
                'city'       => $data['address']['city'],
                'country'    => $data['address']['country'],
                'line'       => $data['address']['line'][0],
                'postalCode' => $data['address']['postalCode'],
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

        foreach ($data['address']['extension'][0]['extension'] as $extensionItem) {
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

    public function getId($response): array
    {
        $data = json_decode($response, true);
        $resType = $data['resourceType'];
        if ($resType == 'Location') {
            return [
                'status'   => true,
                'response' => $data
            ];
        }
        return Error::checkOperationOutcome($resType, $data);
    }
}
