<?php

namespace Rsudipodev\BridgingSatusehat\Bridge\Response;

class Patient
{
    public function convert(string $response)
    {
        $data = json_decode($response, true);
        dd($data);
        if ($data['resourceType'] !== 'Patient') {
            return Error::checkOperationOutcome($data['resourceType'], $data);
        }

        return Error::response($data);
    }

    public function getId(string $response)
    {
        $data = json_decode($response, true);
        $resType = $data['resourceType'];
        if ($resType == 'Patient') {
            return [
                'status'   => true,
                'message'  => 'success',
                'response' => $this->extractPatientData($data)
            ];
        }
        return Error::checkOperationOutcome($resType, $data);
    }

    public function getNik(string $response)
    {
        if (!Error::searchIsEmpty($response)) {
            $data = json_decode($response, true);
            $entry = $data['entry'] ?? [];

            foreach ($entry as $item) {
                $resource = $item['resource'];
                if ($resource['resourceType'] === 'Patient') {
                    return [
                        'status' => true,
                        'message' => 'success',
                        'response' => $this->extractPatientData($resource)
                    ];
                }
            }

            return [
                'status' => false,
                'message' => 'No patient data found.'
            ];
        }

        return [
            'error'   => 'empty-result',
            'message' => 'No data found.'
        ];
    }

    public function getNikIbu(string $response)
    {
        if (!Error::searchIsEmpty($response)) {
            $data = json_decode($response, true);
            $entries = $data['entry'] ?? [];

            $patients = [];
            foreach ($entries as $entry) {
                $resource = $entry['resource'];
                if ($resource['resourceType'] === 'Patient') {
                    $patientData['active'] = $resource['active'] ?? null;
                    $patientData['ihs_number'] = $resource['id'] ?? null;
                    $patientData['name'] = $resource['name'][0]['text'] ?? null;
                    $patientData['birthDate'] = $resource['birthDate'] ?? null;
                    $patientData['nik-ibu'] = $resource['identifier'][0]['value'] ?? null;
                    $patientData['last_update'] = $resource['meta']['lastUpdated'] ?? null;
                    $patients[] = $patientData;
                }
            }

            if (!empty($patients)) {
                return [
                    'status' => true,
                    'message' => 'success',
                    'response' => $patients
                ];
            }

            return [
                'status' => false,
                'error' => 'not-found',
                'message' => 'No patient data found.'
            ];
        }

        return [
            'status'  => false,
            'error'   => 'empty-result',
            'message' => 'No data found.'
        ];
    }

    private function extractPatientData(array $data): array
    {
        return [
            'active' => $data['active'] ?? null,
            'nik' => $data['identifier'][1]['value'] ?? null,
            'ihs_number' => $data['id'] ?? null,
            'name' => $data['name'][0]['text'] ?? null,
            'last_update' => $data['meta']['lastUpdated'] ?? null
        ];
    }
}
