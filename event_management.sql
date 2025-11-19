CREATE DATABASE event_management;
USE event_management;

-- Table des événements
CREATE TABLE IF NOT EXISTS evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    personnes_maximum INT NOT NULL,
    lieu VARCHAR(255) NOT NULL
);

-- Table des inscriptions
CREATE TABLE IF NOT EXISTS inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id INT NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id)
);

DELIMITER $$

-- Créer un événement
DROP PROCEDURE IF EXISTS creer_evenement $$
CREATE PROCEDURE creer_evenement (
    IN p_nom VARCHAR(255),
    IN p_date_debut DATETIME,
    IN p_date_fin DATETIME,
    IN p_personnes_maximum INT,
    IN p_lieu VARCHAR(255)
)
BEGIN
    INSERT INTO evenements (
        nom,
        date_creation,
        date_debut,
        date_fin,
        personnes_maximum,
        lieu
    )
    VALUES (
        p_nom,
        NOW(),
        p_date_debut,
        p_date_fin,
        p_personnes_maximum,
        p_lieu
    );
END $$


-- Inscrire une personne (échoue si max dépassé)
DROP PROCEDURE IF EXISTS inscrire_personne $$
CREATE PROCEDURE inscrire_personne (
    IN p_evenement_id INT,
    IN p_prenom VARCHAR(100),
    IN p_nom VARCHAR(100)
)
BEGIN
    DECLARE v_personnes_max INT;
    DECLARE v_deja_inscrits INT;

    SELECT personnes_maximum
    INTO v_personnes_max
    FROM evenements
    WHERE id = p_evenement_id;

    IF v_personnes_max IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Evenement introuvable';
    END IF;

    SELECT COUNT(*)
    INTO v_deja_inscrits
    FROM inscriptions
    WHERE evenement_id = p_evenement_id;

    IF v_deja_inscrits >= v_personnes_max THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Nombre maximum de personnes deja atteint';
    ELSE
        INSERT INTO inscriptions (
            evenement_id,
            prenom,
            nom,
            date_inscription
        )
        VALUES (
            p_evenement_id,
            p_prenom,
            p_nom,
            NOW()
        );
    END IF;
END $$


-- Désinscrire une personne par prénom + nom
DROP PROCEDURE IF EXISTS desinscrire_personne $$
CREATE PROCEDURE desinscrire_personne (
    IN p_prenom VARCHAR(100),
    IN p_nom VARCHAR(100)
)
BEGIN
    DELETE FROM inscriptions
    WHERE prenom = p_prenom
    AND nom = p_nom;
END $$


-- Supprimer un événement + toutes ses inscriptions
DROP PROCEDURE IF EXISTS supprimer_evenement $$
CREATE PROCEDURE supprimer_evenement (
    IN p_evenement_id INT
)
BEGIN
    START TRANSACTION;

    DELETE FROM inscriptions
    WHERE evenement_id = p_evenement_id;

    DELETE FROM evenements
    WHERE id = p_evenement_id;

    COMMIT;
END $$


-- Changer les dates d’un événement
DROP PROCEDURE IF EXISTS changer_dates_evenement $$
CREATE PROCEDURE changer_dates_evenement (
    IN p_evenement_id INT,
    IN p_nouvelle_date_debut DATETIME,
    IN p_nouvelle_date_fin DATETIME
)
BEGIN
    UPDATE evenements
    SET date_debut = p_nouvelle_date_debut,
        date_fin   = p_nouvelle_date_fin
    WHERE id = p_evenement_id;
END $$

DELIMITER ;

-- Création de l'utilisateur SGBDR restreint
CREATE USER 'event_app'@'localhost' IDENTIFIED BY 'unMotDePasse';

GRANT SELECT ON event_management.* TO 'event_app'@'localhost';
GRANT EXECUTE ON event_management.* TO 'event_app'@'localhost';