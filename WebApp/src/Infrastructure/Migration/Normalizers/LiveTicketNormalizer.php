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
            'date_fin' => $eventData['end'] ?? $eventData['start'],
            'personnes_maximum' => $eventData['max'] ?? 0,
            'lieu' => $eventData['where'] ?? '',
            'attendees' => $this->normalizeAttendees($eventData['attendees'] ?? [])
        ];
    }

    private function normalizeAttendees(array $attendees): array
    {
        $normalized = [];
        foreach ($attendees as $att) {
            if (isset($att['fn'], $att['ln'])) {
                $normalized[] = ['prenom' => $att['fn'], 'nom' => $att['ln']];
            }
        }
        return $normalized;
    }
}
