<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\Response;

use Dflydev\DotAccessData\Data;

class Organization
{
    public function convert($response): array
    {
        $data = json_decode($response, true);
        $resType = $data['resourceType'];
        if ($resType == 'Organization') {
            $organizationData = [
                'active' => $data['active'],
                'ihs_number' => $data['id'],
                'name' => $data['name'],
                'address' => [
                    'city' => $data['address'][0]['city'],
                    'country' => $data['address'][0]['country'],
                    'line' => $data['address'][0]['line'][0],
                    'postalCode' => $data['address'][0]['postalCode'],
                ],
                'contact' => [],
                'partOf' => $data['partOf']['reference'],
                'last_update' => $data['meta']['lastUpdated'],
            ];
            foreach ($data['telecom'] as $telecomItem) {
                $organizationData['contact'][] = [
                    'system' => $telecomItem['system'],
                    'value' => $telecomItem['value'],
                    'use' => $telecomItem['use']
                ];
            }
            foreach ($data['address'][0]['extension'][0]['extension'] as $extensionItem) {
                $organizationData['address']['extension'][] = [
                    'url' => $extensionItem['url'],
                    'valueCode' => $extensionItem['valueCode']
                ];
            }
            return [
                'status' => true,
                'response' => $organizationData
            ];
        }
        return Error::checkOperationOutcome($resType, $data);
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
        // dd($data);
        $resType = $data['resourceType'];
        $entry = $data['entry'] ?? false;
        if ($entry) {
            $dataEntry = [];
            foreach ($entry as $item) {
                $resource = $item['resource'];
                $resType = $resource['resourceType'];

                if ($resType == 'Organization') {
                    $organizationData = [
                        'active' => $resource['active'],
                        'ihs_number' => $resource['id'],
                        'name' => $resource['name'],
                        'contact' => [],
                        'lest_update' => $resource['meta']['lastUpdated'],
                    ];

                    // Check if 'telecom' key exists before trying to iterate
                    if (isset($resource['telecom']) && is_array($resource['telecom'])) {
                        foreach ($resource['telecom'] as $telecomItem) {
                            $organizationData['contact'][] = [
                                'system' => $telecomItem['system'],
                                'value' => $telecomItem['value'],
                                'use' => $telecomItem['use']
                            ];
                        }
                    }

                    $dataEntry[] = $organizationData;
                }
            }
            return [
                'status' => true,
                'total' => count($dataEntry),
                'response' => $dataEntry
            ];
        }
        return [
            'status' => false,
            'message' => 'Data tidak ditemukan!'
        ];
    }

    public static function getPartOf($response): array
    {
        if (!Error::searchIsEmpty($response)) {
            $data = json_decode($response, true);
            // dd($data);
            $entry = $data['entry'] ?? false;
            if ($entry) {
                $dataEntry = [];
                foreach ($entry as $item) {
                    $resource = $item['resource'];
                    $resType = $resource['resourceType'];
                    if ($resType == 'Organization') {
                        $organizationData = [
                            'ihs_number' => $resource['id'],
                            'name' => $resource['name'],
                            'kode' => $resource['identifier'][0]['value'],
                            'partOf' => $resource['partOf']['reference'],
                            'contact' => [],
                            'lest_update' => $resource['meta']['lastUpdated'],
                        ];
                        // Check if 'telecom' key exists before trying to iterate
                        if (isset($resource['telecom']) && is_array($resource['telecom'])) {
                            foreach ($resource['telecom'] as $telecomItem) {
                                $organizationData['contact'][] = [
                                    'system' => $telecomItem['system'],
                                    'value' => $telecomItem['value'],
                                    'use' => $telecomItem['use']
                                ];
                            }
                        }

                        $dataEntry[] = $organizationData;
                    }
                }
                return [
                    'status' => true,
                    'res' => $dataEntry
                ];
            }
            return [
                'status' => false,
                'message' => $response
            ];
        }
        return [
            'status' => false,
            'message' => 'Data tidak ditemukan!'
        ];
    }
}
