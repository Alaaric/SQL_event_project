<?php

namespace EventApp\Infrastructure\Controller;

use EventApp\Infrastructure\Repository\MongoEventRepository;
use EventApp\Infrastructure\Repository\MySQLEventRepository;
use EventApp\Infrastructure\Repository\MySQLInscriptionRepository;
use EventApp\Infrastructure\Database\DatabaseConnection;
use EventApp\Infrastructure\Migration\MongoToMySQLMigrator;
use EventApp\Infrastructure\Migration\Normalizers\LiveTicketNormalizer;
use EventApp\Infrastructure\Migration\Normalizers\TrueGisterNormalizer;
use EventApp\Infrastructure\Migration\Normalizers\DiSiSFineNormalizer;
use MongoDB\Client as MongoClient;

class MigrationController
{
    private MongoEventRepository $mongoEventRepository;
    private MySQLEventRepository $eventRepository;
    private MySQLInscriptionRepository $inscriptionRepository;

    public function __construct()
    {
        $pdo = DatabaseConnection::getInstance();
        $this->eventRepository = new MySQLEventRepository($pdo);
        $this->inscriptionRepository = new MySQLInscriptionRepository($pdo);

        $mongo = new MongoClient($_ENV['MONGODB_URI']);
        $this->mongoEventRepository = new MongoEventRepository($mongo);
    }

    public function migrate(): array
    {
        $normalizers = [
            new LiveTicketNormalizer(),
            new TrueGisterNormalizer(),
            new DiSiSFineNormalizer(),
        ];

        $migrator = new MongoToMySQLMigrator(
            $this->mongoEventRepository,
            $this->eventRepository,
            $this->inscriptionRepository,
            $normalizers
        );

        return $migrator->migrate();
    }
}
