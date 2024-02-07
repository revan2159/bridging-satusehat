<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\Response;

class Practitioner
{
    public function getNik(string $response): array
    {
        if (!Error::searchIsEmpty($response)) {
            $data = json_decode($response, true);
            $entry = $data['entry'] ?? false;
            if ($entry) {
                $resource = $entry[0]['resource'];
                return $this->processResourceData($resource, $data);
            }
            return [
                'status'  => false,
                'message' => $response
            ];
        }
        return [
            'status'  => false,
            'message' => 'Data tidak ditemukan!'
        ];
    }

    public function getId(string $response): array
    {
        $data = json_decode($response, true);
        $resType = $data['resourceType'];
        if ($resType == 'Practitioner') {
            return [
                'status'   => true,
                'response' => $data
            ];
        }
        return Error::checkOperationOutcome($resType, $data);
    }

    private function processResourceData(array $resource, array $data): array
    {
        $resType = $resource['resourceType'];
        if ($resType == 'Practitioner') {
            return [
                'status'   => true,
                'total'    => $data['total'],
                'response' => [
                    'ihs_number' => $resource['id'],
                    'nik'        => $resource['identifier'][1]['value'],
                    'name'       => $resource['name'][0]['text'],
                    'update'     => $resource['meta']['lastUpdated'],
                ]
            ];
        }
        return Error::checkOperationOutcome($resType, $data);
    }
}
