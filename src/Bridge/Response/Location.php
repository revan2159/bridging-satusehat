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

        $locationData = $this->extractLocationData($data);

        // Extract contact information
        $locationData['contact'] = array_map(function ($telecomItem) {
            return [
                'system' => $telecomItem['system'],
                'value'  => $telecomItem['value'],
                'use'    => $telecomItem['use']
            ];
        }, $data['telecom']);

        // Extract address extensions
        $locationData['address']['extension'] = array_map(function ($extensionItem) {
            return [
                'url'       => $extensionItem['url'],
                'valueCode' => $extensionItem['valueCode']
            ];
        }, $data['address']['extension'][0]['extension']);

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
                'message'  => 'success',
                'response' => $data
            ];
        }
        return Error::checkOperationOutcome($resType, $data);
    }

    public function getOrgId($response): array
    {

        $data = json_decode($response, true);

        if (empty($data['entry'])) {
            return [
                'status'  => false,
                'error'   => 'not-found',
                'message' => 'The reference provided was not found.'
            ];
        }

        $locationData = [];
        foreach ($data['entry'] as $item) {
            $resource = $item['resource'];
            if ($resource['resourceType'] === 'Location') {
                $locationData[] = $this->extractLocationData($resource);
            }
        }

        $resType = $data['resourceType'];
        if ($resType == 'Bundle') {
            return [
                'status'   => true,
                'message'  => 'success',
                'total'    => count($locationData),
                'response' => $locationData
            ];
        }
        return Error::checkOperationOutcome($resType, $data);
    }

    private function extractLocationData(array $resource): array
    {
        return [
            'active'      => $resource['status'],
            'ihs_number'  => $resource['id'],
            'identifier'  => $resource['identifier'][0]['value'],
            'name'        => $resource['name'],
            'description' => $resource['description'],
            'type'        => $resource['physicalType']['coding'][0]['display'],
            'type_code'   => $resource['physicalType']['coding'][0]['code'],
            'address'     => [
                'city'       => $resource['address']['city'],
                'country'    => $resource['address']['country'],
                'line'       => $resource['address']['line'][0],
                'postalCode' => $resource['address']['postalCode'],
                'extension'  => [],
            ],
            'position'    => [
                'latitude'  => $resource['position']['latitude'],
                'longitude' => $resource['position']['longitude'],
                'altitude'  => $resource['position']['altitude']
            ],
            'contact'     => [],
            'managingOrganization' => $resource['managingOrganization']['reference'],
            'last_update' => $resource['meta']['lastUpdated'],
        ];
    }
}
