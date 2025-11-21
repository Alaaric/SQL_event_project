<?php

namespace EventApp\Infrastructure\Migration\Normalizers;

class TrueGisterNormalizer implements EventNormalizerInterface
{
    public function canNormalize(array $eventData): bool
    {
        return isset($eventData['results'][0]['event']);
    }

    public function normalize(array $eventData): array
    {
        $event = $eventData['results'][0]['event'];
        return [
            'nom' => $event['event_name'],
            'date_debut' => $event['event_begin'],
            'date_fin' => $event['event_finish'],
            'personnes_maximum' => null,
            'lieu' => $event['event_where'] ?? '',
            'attendees' => $this->normalizeAttendees($eventData['results'][0]['attendees'] ?? [])
        ];
    }

    private function normalizeAttendees(array $attendees): array
    {
        $normalized = [];
        foreach ($attendees as $attendee) {
            if (isset($attendee['attendee_1'], $attendee['attendee_2'])) {
                $normalized[] = ['prenom' => $attendee['attendee_1'], 'nom' => $attendee['attendee_2']];
            }
        }
        return $normalized;
    }
}
