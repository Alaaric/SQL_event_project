<?php

namespace EventApp\Infrastructure\Migration\Normalizers;

class DiSiSFineNormalizer implements EventNormalizerInterface
{
    public function canNormalize(array $eventData): bool
    {
        return isset($eventData['e_name']);
    }

    public function normalize(array $eventData): array
    {
        return [
            'nom' => $eventData['e_name'],
            'date_debut' => $eventData['e_start'],
            'date_fin' => $eventData['e_finish'],
            'personnes_maximum' => $eventData['e_attendees_max'],
            'lieu' => $eventData['e_location'],
            'attendees' => $this->normalizeAttendees($eventData['attendees'])
        ];
    }

    private function normalizeAttendees(string $attendeesJson): array
    {
        if (empty($attendeesJson)) {
            return [];
        }

        $decoded = json_decode($attendeesJson, true);
        if (!is_array($decoded)) {
            return [];
        }

        $normalized = [];
        foreach ($decoded as $att) {
            if (is_array($att) && count($att) >= 2) {
                $normalized[] = [
                    'prenom' => trim($att[0]),
                    'nom' => trim($att[1]),
                    'date_inscription' => trim($att[2])
                ];
            }
        }

        return $normalized;
    }
}
