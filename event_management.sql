-- On a des drop/creates pour faciliter les tests répétés
DROP DATABASE IF EXISTS event_management;
CREATE DATABASE event_management;
USE event_management;

CREATE TABLE IF NOT EXISTS evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    personnes_maximum INT NULL, -- info manquante dans un des JSON exemples donc NULL autorisé ici
    lieu VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id INT NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE
);

DELIMITER //

-- Créer un événement
DROP PROCEDURE IF EXISTS CreerEvenement //
CREATE PROCEDURE CreerEvenement (
    IN nom_evenement VARCHAR(255),
    IN date_debut DATETIME,
    IN date_fin DATETIME,
    IN personnes_maximum INT,
    IN lieu VARCHAR(255),
    OUT evenement_id INT
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
        nom_evenement,
        NOW(),
        date_debut,
        date_fin,
        personnes_maximum,
        lieu
    );
        SET evenement_id = LAST_INSERT_ID();
END //


-- Inscrire une personne à un événement
DROP PROCEDURE IF EXISTS InscrirePersonne //
CREATE PROCEDURE InscrirePersonne (
    IN event_id INT,
    IN first_name VARCHAR(100),
    IN last_name VARCHAR(100),
    IN registration_date DATETIME
)
BEGIN
    DECLARE limite_participants INT;
    DECLARE nb_participants_actuels INT;

    -- On check en amont si notre evenement existe bien même si la FK devrait déjà le faire à l'insertion
    IF NOT EXISTS (SELECT 1 FROM evenements WHERE id = event_id) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Evenement introuvable';
    END IF;

    SELECT personnes_maximum
    INTO limite_participants
    FROM evenements
    WHERE id = event_id;

    -- On check si notre max de participants est atteint s'il y a une limite renseignée
    IF limite_participants IS NOT NULL THEN
        SELECT COUNT(*) INTO nb_participants_actuels
        FROM inscriptions
        WHERE evenement_id = event_id;

        IF nb_participants_actuels >= limite_participants THEN
            SIGNAL SQLSTATE '45001'
                SET MESSAGE_TEXT = 'Nombre maximum de personnes deja atteint';
        END IF;
    END IF;

    -- On check si la personne est pas déjà inscrite pour éviter les doublons
    IF EXISTS (SELECT 1 FROM inscriptions 
               WHERE evenement_id = event_id 
               AND prenom = first_name 
               AND nom = last_name) THEN
        SIGNAL SQLSTATE '45002'
            SET MESSAGE_TEXT = 'Cette personne est deja inscrite a cet evenement';
    END IF;
    
    -- Si on a la date, on l'utilise, sinon on laisse MySQL mettre le DEFAULT.
    -- Est-ce propre de laisser la table gérer cette valeur par defaut? pas sûr ¯\_(ツ)_/¯
    IF registration_date IS NOT NULL THEN
        INSERT INTO inscriptions (
            evenement_id,
            prenom,
            nom,
            date_inscription
        )
        VALUES (
            event_id,
            first_name,
            last_name,
            registration_date
        );
    ELSE
        INSERT INTO inscriptions (
            evenement_id,
            prenom,
            nom
        )
        VALUES (
            event_id,
            first_name,
            last_name
        );
    END IF;
    
    -- Récupérer la date réellement insérée (soit celle fournie, soit NOW() via DEFAULT)
    SELECT 
        id,
        evenement_id,
        prenom,
        nom,
        date_inscription
    FROM inscriptions
    WHERE id = LAST_INSERT_ID();
END //


-- Désinscrire une personne par prénom + nom
DROP PROCEDURE IF EXISTS DesinscrirePersonne //
CREATE PROCEDURE DesinscrirePersonne (
    IN first_name VARCHAR(100),
    IN last_name VARCHAR(100)
)
BEGIN
    DELETE FROM inscriptions
    WHERE prenom = first_name
    AND nom = last_name;
END //


-- Supprimer un événement (inscriptions seront supprimées automatiquement via CASCADE sur la FK)
DROP PROCEDURE IF EXISTS SupprimerEvenement //
CREATE PROCEDURE SupprimerEvenement(IN event_id INT)
BEGIN
    DELETE FROM evenements WHERE id = event_id;
END //

-- Modifier les dates d'un événement
DROP PROCEDURE IF EXISTS ModifierDatesEvenement //
CREATE PROCEDURE ModifierDatesEvenement (
    IN event_id INT,
    IN new_start_date DATETIME,
    IN new_end_date DATETIME
)
BEGIN
    UPDATE evenements
    SET date_debut = new_start_date,
        date_fin   = new_end_date
    WHERE id = event_id;
END //

DELIMITER ;

