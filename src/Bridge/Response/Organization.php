<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\Response;

class Organization
{
    public function convert($response): array
    {
        $data = json_decode($response, true);

        if ($data['resourceType'] !== 'Organization') {
            return Error::checkOperationOutcome($data['resourceType'], $data);
        }

        $organizationData = [
            'active'      => $data['active'],
            'ihs_number'  => $data['id'],
            'identifier'  => $data['identifier'][0]['value'],
            'name'        => $data['name'],
            'address'     => [
                'city'       => $data['address'][0]['city'],
                'country'    => $data['address'][0]['country'],
                'line'       => $data['address'][0]['line'][0],
                'postalCode' => $data['address'][0]['postalCode'],
                'extension'  => [],
            ],
            'contact'     => [],
            'partOf'      => $data['partOf']['reference'],
            'last_update' => $data['meta']['lastUpdated'],
        ];

        foreach ($data['telecom'] as $telecomItem) {
            $organizationData['contact'][] = [
                'system' => $telecomItem['system'],
                'value'  => $telecomItem['value'],
                'use'    => $telecomItem['use']
            ];
        }

        foreach ($data['address'][0]['extension'][0]['extension'] as $extensionItem) {
            $organizationData['address']['extension'][] = [
                'url'       => $extensionItem['url'],
                'valueCode' => $extensionItem['valueCode']
            ];
        }

        return [
            'status'   => true,
            'message'  => 'success',
            'response' => $organizationData
        ];
    }


    public function getId($response): array
    {
        $data = json_decode($response, true);
        $resType = $data['resourceType'];
        if ($resType == 'Organization') {
            return [
                'status' => true,
                'response' => $data
            ];
        }
        return Error::checkOperationOutcome($resType, $data);
    }

    public function getName($response): array
    {
        $data = json_decode($response, true);

        if (empty($data['entry'])) {
            return [
                'status'  => false,
                'message' => 'Data tidak ditemukan!'
            ];
        }

        $dataEntry = [];

        foreach ($data['entry'] as $item) {
            $resource = $item['resource'];

            if ($resource['resourceType'] === 'Organization') {
                $organizationData = [
                    'active'      => $resource['active'],
                    'ihs_number'  => $resource['id'],
                    'name'        => $resource['name'],
                    'contact'     => [],
                    'last_update' => $resource['meta']['lastUpdated'],
                ];

                // Check if 'telecom' key exists before trying to iterate
                if (isset($resource['telecom']) && is_array($resource['telecom'])) {
                    foreach ($resource['telecom'] as $telecomItem) {
                        $organizationData['contact'][] = [
                            'system' => $telecomItem['system'],
                            'value'  => $telecomItem['value'],
                            'use'    => $telecomItem['use']
                        ];
                    }
                }

                $dataEntry[] = $organizationData;
            }
        }

        return [
            'status'   => true,
            'total'    => count($dataEntry),
            'response' => $dataEntry
        ];
    }


    public static function getPartOf($response): array
    {
        $responseData = json_decode($response, true);

        if (Error::searchIsEmpty($responseData)) {
            return [
                'status'  => false,
                'message' => 'Data tidak ditemukan!'
            ];
        }

        $entry = $responseData['entry'] ?? [];

        if (empty($entry)) {
            return [
                'status'  => false,
                'message' => 'Data tidak ditemukan!'
            ];
        }

        $dataEntry = [];

        foreach ($entry as $item) {
            $resource = $item['resource'];

            if ($resource['resourceType'] === 'Organization') {
                $organizationData = [
                    'ihs_number'  => $resource['id'],
                    'name'        => $resource['name'],
                    'kode'        => $resource['identifier'][0]['value'],
                    'partOf'      => $resource['partOf']['reference'],
                    'contact'     => [],
                    'last_update' => $resource['meta']['lastUpdated'],
                ];

                // Check if 'telecom' key exists before trying to iterate
                if (isset($resource['telecom']) && is_array($resource['telecom'])) {
                    foreach ($resource['telecom'] as $telecomItem) {
                        $organizationData['contact'][] = [
                            'system' => $telecomItem['system'],
                            'value'  => $telecomItem['value'],
                            'use'    => $telecomItem['use']
                        ];
                    }
                }

                $dataEntry[] = $organizationData;
            }
        }

        return [
            'status'   => true,
            'total'    => count($dataEntry),
            'response' => $dataEntry
        ];
    }
}
