<?php

namespace EventApp\Infrastructure\Database;

use PDO;

class DatabaseConnection
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            if (!isset($_ENV['MYSQL_DSN'])) {
                $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../..');
                $dotenv->load();
            }

            self::$instance = new PDO(
                $_ENV['MYSQL_DSN'],
                $_ENV['MYSQL_USER'],
                $_ENV['MYSQL_PASSWORD'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }

        return self::$instance;
    }
}
