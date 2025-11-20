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
        $evt = $eventData['results'][0]['event'];
        return [
            'nom' => $evt['event_name'],
            'date_debut' => $evt['event_begin'],
            'date_fin' => $evt['event_finish'] ?? $evt['event_begin'],
            'personnes_maximum' => 0,
            'lieu' => $evt['event_where'] ?? '',
            'attendees' => $this->normalizeAttendees($eventData['results'][0]['attendees'] ?? [])
        ];
    }

    private function normalizeAttendees(array $attendees): array
    {
        $normalized = [];
        foreach ($attendees as $att) {
            if (isset($att['attendee_1'], $att['attendee_2'])) {
                $normalized[] = ['prenom' => $att['attendee_1'], 'nom' => $att['attendee_2']];
            }
        }
        return $normalized;
    }
}
