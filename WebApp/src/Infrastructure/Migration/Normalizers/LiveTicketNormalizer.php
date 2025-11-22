<?php

namespace EventApp\Infrastructure\Migration\Normalizers;

class LiveTicketNormalizer implements EventNormalizerInterface
{
    public function canNormalize(array $eventData): bool
    {
        return isset($eventData['event']);
    }

    public function normalize(array $eventData): array
    {
        return [
            'nom' => $eventData['event'],
            'date_debut' => $eventData['start'],
            'date_fin' => $eventData['end'],
            'personnes_maximum' => $eventData['max'],
            'lieu' => $eventData['where'],
            'attendees' => $this->normalizeAttendees($eventData['attendees'])
        ];
    }

    private function normalizeAttendees(array $attendees): array
    {
        $normalized = [];
        foreach ($attendees as $attendee) {
            if (isset($attendee['fn'], $attendee['ln'])) {
                $normalized[] = [
                    'prenom' => $attendee['fn'],
                    'nom' => $attendee['ln'],
                    'date_inscription' => $attendee['when']
                ];
            }
        }
        return $normalized;
    }
}
