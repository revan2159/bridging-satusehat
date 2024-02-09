<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\Response;

class Organization
{
    public function convert(string $response): array
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
            'contact'     => $this->extractContactInfo($data),
            'partOf'      => $data['partOf']['reference'],
            'last_update' => $data['meta']['lastUpdated'],
        ];

        // Extract address extensions
        $organizationData['address']['extension'] = $this->extractAddressExtensions($data);

        return [
            'status'   => true,
            'message'  => 'success',
            'response' => $organizationData
        ];
    }

    public function getId(string $response): array
    {
        $data = json_decode($response, true);
        $resType = $data['resourceType'];
        if ($resType == 'Organization') {
            return [
                'status'   => true,
                'message'  => 'success',
                'response' => $data
            ];
        }
        return Error::checkOperationOutcome($resType, $data);
    }

    public function getName(string $response): array
    {
        $data = json_decode($response, true);

        if (empty($data['entry'])) {
            return [
                'status'  => false,
                'error'   => 'not-found',
                'message' => 'The reference provided was not found.'
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
                    'contact'     => $this->extractContactInfo($resource),
                    'last_update' => $resource['meta']['lastUpdated'],
                ];

                $dataEntry[] = $organizationData;
            }
        }

        return [
            'status'   => true,
            'message'  => 'success',
            'total'    => count($dataEntry),
            'response' => $dataEntry
        ];
    }

    public function getPartOf(string $response): array
    {
        $responseData = json_decode($response, true);

        if (Error::searchIsEmpty($responseData) || empty($responseData['entry'])) {
            return [
                'status'  => false,
                'error'   => 'empty-result',
                'message' => 'No data found.'
            ];
        }

        $dataEntry = [];
        foreach ($responseData['entry'] as $item) {
            $resource = $item['resource'];
            if ($resource['resourceType'] === 'Organization') {
                $organizationData = [
                    'ihs_number'  => $resource['id'],
                    'name'        => $resource['name'],
                    'kode'        => $resource['identifier'][0]['value'],
                    'partOf'      => $resource['partOf']['reference'],
                    'contact'     => $this->extractContactInfo($resource),
                    'last_update' => $resource['meta']['lastUpdated'],
                ];

                $dataEntry[] = $organizationData;
            }
        }

        return [
            'status'   => true,
            'message'  => 'success',
            'total'    => count($dataEntry),
            'response' => $dataEntry
        ];
    }

    private function extractContactInfo(array $data): array
    {
        $contactInfo = [];
        if (isset($data['telecom']) && is_array($data['telecom'])) {
            foreach ($data['telecom'] as $telecomItem) {
                $contactInfo[] = [
                    'system' => $telecomItem['system'],
                    'value'  => $telecomItem['value'],
                    'use'    => $telecomItem['use']
                ];
            }
        }
        return $contactInfo;
    }

    private function extractAddressExtensions(array $data): array
    {
        $extensions = [];
        if (isset($data['address'][0]['extension'][0]['extension'])) {
            foreach ($data['address'][0]['extension'][0]['extension'] as $extensionItem) {
                $extensions[] = [
                    'url'       => $extensionItem['url'],
                    'valueCode' => $extensionItem['valueCode']
                ];
            }
        }
        return $extensions;
    }
}
