<?php

namespace EventApp\Infrastructure\Migration;

use EventApp\Domain\Interfaces\MongoEventRepositoryInterface;
use EventApp\Domain\Interfaces\EventRepositoryInterface;
use EventApp\Domain\Interfaces\InscriptionRepositoryInterface;
use EventApp\Infrastructure\DTO\CreateEventDTO;
use EventApp\Infrastructure\DTO\CreateInscriptionDTO;
use EventApp\Infrastructure\Migration\Normalizers\EventNormalizerInterface;

class MongoToMySQLMigrator
{
    /** @var EventNormalizerInterface[] */
    private array $normalizers;

    public function __construct(
        private MongoEventRepositoryInterface $mongoEventRepository,
        private EventRepositoryInterface $eventRepository,
        private InscriptionRepositoryInterface $inscriptionRepository,
        array $normalizers = []
    ) {
        $this->normalizers = $normalizers;
    }

    public function migrate(): array
    {
        $events = $this->mongoEventRepository->findAllRawEvents();

        $migrated = 0;
        $errors = [];

        foreach ($events as $eventArray) {
            try {
                $normalizedData = $this->normalizeEvent($eventArray);
                $this->createEventWithInscriptions($normalizedData);

                $this->mongoEventRepository->markAsMigrated($eventArray['_id_object']);

                $migrated++;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return ['migrated' => $migrated, 'errors' => $errors];
    }

    private function normalizeEvent(array $eventData): array
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->canNormalize($eventData)) {
                return $normalizer->normalize($eventData);
            }
        }

        throw new \Exception('Format non reconnu pour l\'événement: ' . json_encode(array_keys($eventData)));
    }

    private function createEventWithInscriptions(array $eventData): void
    {
        $eventDTO = new CreateEventDTO(
            $eventData['nom'],
            new \DateTime($eventData['date_debut']),
            new \DateTime($eventData['date_fin']),
            $eventData['personnes_maximum'],
            $eventData['lieu']
        );

        $createdEvent = $this->eventRepository->create($eventDTO);

        foreach ($eventData['attendees'] as $attendee) {
            $inscriptionDTO = new CreateInscriptionDTO(
                $createdEvent->id,
                $attendee['prenom'],
                $attendee['nom']
            );

            $this->inscriptionRepository->create(
                $inscriptionDTO,
                $attendee['date_inscription'] ?? null
            );
        }
    }
}
