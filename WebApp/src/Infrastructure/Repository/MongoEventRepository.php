<?php

namespace EventApp\Infrastructure\Repository;

use EventApp\Domain\Interfaces\MongoEventRepositoryInterface;
use MongoDB\Client as MongoClient;
use MongoDB\Collection;

class MongoEventRepository implements MongoEventRepositoryInterface
{
    private const COLLECTION_NAME = 'events';
    private Collection $collection;

    public function __construct(
        private MongoClient $mongoClient
    ) {
        $databaseName = $_ENV['MONGODB_DATABASE'];
        $this->collection = $this->mongoClient->{$databaseName}->{self::COLLECTION_NAME};
    }

    public function findAllRawEvents(): array
    {
        $events = iterator_to_array($this->collection->find(['migrated' => ['$ne' => true]]));

        return array_map(function ($mongoEvent) {
            $data = json_decode(json_encode($mongoEvent->bsonSerialize()), true);
            $data['_id_object'] = $mongoEvent->_id;
            return $data;
        }, $events);
    }

    public function markAsMigrated($eventId): void
    {
        $this->collection->updateOne(
            ['_id' => $eventId],
            ['$set' => ['migrated' => true]]
        );
    }
}
