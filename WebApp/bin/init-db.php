#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$sqlFile = __DIR__ . '/../../event_management.sql';

$appUser = $_ENV['MYSQL_USER'];
$appPassword = $_ENV['MYSQL_PASSWORD'];
$adminUser = $_ENV['MYSQL_ADMIN_USER'] ?? 'root';

$userSql = "
DROP USER IF EXISTS '$appUser'@'localhost';
CREATE USER '$appUser'@'localhost' IDENTIFIED BY '$appPassword';
GRANT SELECT ON event_management.* TO '$appUser'@'localhost';
GRANT EXECUTE ON event_management.* TO '$appUser'@'localhost';
FLUSH PRIVILEGES;
";

$tempSqlFile = sys_get_temp_dir() . '/setup.sql';
file_put_contents($tempSqlFile, file_get_contents($sqlFile) . $userSql);


$mysqlCmd = "mysql -u $adminUser -p < " . escapeshellarg($tempSqlFile);
$returnCode = 0;
passthru($mysqlCmd, $returnCode);

unlink($tempSqlFile);

if ($returnCode === 0) {
    echo "Base de données créée\n";
    echo "On peu maintenant lancer l'application :\n";
    echo "php -S localhost:8000 -t public\n
    OU\n
    make start\n";
} else {
    echo "Erreur lors du l'intialisation de la DB (code: $returnCode)\n";
    exit(1);
}
