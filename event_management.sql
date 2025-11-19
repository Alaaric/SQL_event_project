CREATE DATABASE event_management;
USE event_management;

CREATE USER 'app'@'localhost' IDENTIFIED BY 'unMotDePasse';

GRANT SELECT ON event_management.* TO 'app'@'localhost';
GRANT EXECUTE ON event_management.* TO 'app'@'localhost';
FLUSH PRIVILEGES;