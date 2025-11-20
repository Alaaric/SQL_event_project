<?php

namespace EventApp\Infrastructure\Repository;

use EventApp\Domain\Interfaces\MongoEventRepositoryInterface;
use MongoDB\Client as MongoClient;

class MongoEventRepository implements MongoEventRepositoryInterface
{
    public function __construct(
        private MongoClient $mongoClient,
        private string $databaseName = 'event_management',
        private string $collectionName = 'events'
    ) {}

    public function findAllRawEvents(): array
    {
        $collection = $this->mongoClient->{$this->databaseName}->{$this->collectionName};
        $events = iterator_to_array($collection->find());

        return array_map(
            fn($mongoEvent) => json_decode(json_encode($mongoEvent->bsonSerialize()), true),
            $events
        );
    }
}
