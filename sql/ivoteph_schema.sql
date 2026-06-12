-- ============================================================
-- iVotePH MySQL Database Schema
-- Academic online voting simulation for WAMP/MySQL Workbench
-- ============================================================

CREATE DATABASE IF NOT EXISTS ivoteph
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ivoteph;

-- Re-runnable setup for development/testing.
DROP TRIGGER IF EXISTS trg_accounts_before_insert;
DROP TRIGGER IF EXISTS trg_accounts_after_insert;
DROP TRIGGER IF EXISTS trg_accounts_after_delete;
DROP TRIGGER IF EXISTS trg_votes_before_insert;
DROP TRIGGER IF EXISTS trg_votes_no_update;
DROP TRIGGER IF EXISTS trg_votes_no_delete;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS votes;
DROP TABLE IF EXISTS candidates;
DROP TABLE IF EXISTS positions;
DROP TABLE IF EXISTS accounts;
DROP TABLE IF EXISTS registered_voters;
DROP TABLE IF EXISTS elections;
DROP TABLE IF EXISTS admins;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- ADMIN TABLES
-- ============================================================

CREATE TABLE admins (
    admin_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED NULL,
    admin_name VARCHAR(100) NOT NULL,
    action VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_audit_logs_created_at (created_at),
    INDEX idx_audit_logs_admin_id (admin_id),

    CONSTRAINT fk_audit_logs_admin
        FOREIGN KEY (admin_id)
        REFERENCES admins(admin_id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- VOTER REGISTRY AND ACCOUNT REGISTRATION
-- ============================================================

CREATE TABLE registered_voters (
    voter_id VARCHAR(30) PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    email VARCHAR(150) NOT NULL,

    -- Unregistered = eligible voter exists in master list but has no account yet.
    -- Registered = voter has already created one account.
    -- Blocked = admin-disabled voter record.
    registration_status ENUM('Unregistered', 'Registered', 'Blocked') NOT NULL DEFAULT 'Unregistered',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_registered_voters_email (email),
    INDEX idx_registered_voters_name_birthdate (last_name, first_name, birth_date),
    INDEX idx_registered_voters_status (registration_status)
) ENGINE=InnoDB;

CREATE TABLE accounts (
    account_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    voter_id VARCHAR(30) NOT NULL,
    username VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_accounts_voter_id (voter_id),
    UNIQUE KEY uq_accounts_username (username),
    INDEX idx_accounts_active (is_active),

    CONSTRAINT fk_accounts_registered_voter
        FOREIGN KEY (voter_id)
        REFERENCES registered_voters(voter_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- ELECTION SETUP
-- ============================================================

CREATE TABLE positions (
    position_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(100) NOT NULL,

    -- Extra admin-side fields used by the current admin dashboard UI.
    description TEXT NULL,
    max_votes INT UNSIGNED NOT NULL DEFAULT 1,
    display_order INT UNSIGNED NOT NULL DEFAULT 0,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_positions_name (position_name),
    INDEX idx_positions_display_order (display_order)
) ENGINE=InnoDB;

CREATE TABLE candidates (
    candidate_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    political_party VARCHAR(150) NOT NULL,
    position_id INT UNSIGNED NOT NULL,
    photo VARCHAR(255) NULL,
    platform TEXT NOT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_candidates_position_id (position_id),
    UNIQUE KEY uq_candidates_candidate_position (candidate_id, position_id),

    CONSTRAINT fk_candidates_position
        FOREIGN KEY (position_id)
        REFERENCES positions(position_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE elections (
    election_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    election_title VARCHAR(180) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('draft', 'open', 'closed') NOT NULL DEFAULT 'draft',

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_elections_status (status),
    INDEX idx_elections_dates (start_date, end_date)
) ENGINE=InnoDB;

-- ============================================================
-- VOTING TRANSACTION TABLE
-- ============================================================

CREATE TABLE votes (
    vote_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    election_id INT UNSIGNED NOT NULL,
    voter_id VARCHAR(30) NOT NULL,
    candidate_id INT UNSIGNED NOT NULL,
    position_id INT UNSIGNED NOT NULL,
    vote_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- One voter can only vote once per position per election.
    UNIQUE KEY uq_votes_once_per_position (election_id, voter_id, position_id),

    INDEX idx_votes_election_id (election_id),
    INDEX idx_votes_voter_id (voter_id),
    INDEX idx_votes_candidate_id (candidate_id),
    INDEX idx_votes_position_id (position_id),
    INDEX idx_votes_timestamp (vote_timestamp),

    CONSTRAINT fk_votes_election
        FOREIGN KEY (election_id)
        REFERENCES elections(election_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    -- This references accounts instead of only registered_voters, so only voters
    -- who successfully created an account can cast a vote.
    CONSTRAINT fk_votes_account_voter
        FOREIGN KEY (voter_id)
        REFERENCES accounts(voter_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_votes_position
        FOREIGN KEY (position_id)
        REFERENCES positions(position_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    -- Composite FK ensures that the chosen candidate really belongs to the
    -- position recorded in the vote.
    CONSTRAINT fk_votes_candidate_position
        FOREIGN KEY (candidate_id, position_id)
        REFERENCES candidates(candidate_id, position_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- BUSINESS RULE TRIGGERS
-- ============================================================

DELIMITER //

CREATE TRIGGER trg_accounts_before_insert
BEFORE INSERT ON accounts
FOR EACH ROW
BEGIN
    DECLARE voter_status VARCHAR(20) DEFAULT NULL;
    DECLARE missing_voter TINYINT DEFAULT 0;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET missing_voter = 1;

    SELECT registration_status
      INTO voter_status
      FROM registered_voters
     WHERE voter_id = NEW.voter_id
     LIMIT 1;

    IF missing_voter = 1 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Voter record not found.';
    END IF;

    IF voter_status = 'Blocked' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'This voter record is blocked.';
    END IF;
END//

CREATE TRIGGER trg_accounts_after_insert
AFTER INSERT ON accounts
FOR EACH ROW
BEGIN
    UPDATE registered_voters
       SET registration_status = 'Registered'
     WHERE voter_id = NEW.voter_id
       AND registration_status <> 'Blocked';
END//

CREATE TRIGGER trg_accounts_after_delete
AFTER DELETE ON accounts
FOR EACH ROW
BEGIN
    UPDATE registered_voters
       SET registration_status = 'Unregistered'
     WHERE voter_id = OLD.voter_id
       AND registration_status <> 'Blocked';
END//

CREATE TRIGGER trg_votes_before_insert
BEFORE INSERT ON votes
FOR EACH ROW
BEGIN
    DECLARE account_active TINYINT DEFAULT 0;
    DECLARE election_status VARCHAR(20) DEFAULT NULL;
    DECLARE missing_record TINYINT DEFAULT 0;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET missing_record = 1;

    SELECT is_active
      INTO account_active
      FROM accounts
     WHERE voter_id = NEW.voter_id
     LIMIT 1;

    IF missing_record = 1 OR account_active <> 1 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Only active registered voters can vote.';
    END IF;

    SET missing_record = 0;

    SELECT status
      INTO election_status
      FROM elections
     WHERE election_id = NEW.election_id
     LIMIT 1;

    IF missing_record = 1 OR election_status <> 'open' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Voting is not open for this election.';
    END IF;
END//

CREATE TRIGGER trg_votes_no_update
BEFORE UPDATE ON votes
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Votes cannot be edited after submission.';
END//

CREATE TRIGGER trg_votes_no_delete
BEFORE DELETE ON votes
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Votes cannot be deleted after submission.';
END//

DELIMITER ;

-- ============================================================
-- SAMPLE RECORDS FOR TESTING
-- ============================================================

-- Default admin login:
-- Email: admin@ivoteph.test
-- Password: Admin@12345
INSERT INTO admins (admin_name, email, password_hash) VALUES
('System Administrator', 'admin@ivoteph.test', '$2y$12$FOYogfKYxrYqDg3t8r6Jmewz6mUqXgk1DttYKl3eXiUlQ2kQp0nxO');

-- Imported master list of eligible voters.
INSERT INTO registered_voters (voter_id, first_name, last_name, birth_date, email, registration_status) VALUES
('PHV-2025-001', 'Juan', 'Dela Cruz', '1998-06-12', 'juan.delacruz@example.com', 'Unregistered'),
('PHV-2025-002', 'Maria', 'Santos', '1999-03-21', 'maria.santos@example.com', 'Unregistered'),
('PHV-2025-003', 'Jose', 'Reyes', '1997-11-03', 'jose.reyes@example.com', 'Unregistered'),
('PHV-2025-004', 'Ana', 'Garcia', '2000-01-15', 'ana.garcia@example.com', 'Unregistered'),
('PHV-2025-005', 'Miguel', 'Bautista', '1996-09-18', 'miguel.bautista@example.com', 'Blocked'),
('PHV-2025-006', 'Liza', 'Ramos', '2001-02-09', 'liza.ramos@example.com', 'Unregistered');

-- Sample voter accounts.
-- Default sample voter password: Voter@123
-- The account trigger automatically marks these voters as Registered.
INSERT INTO accounts (voter_id, username, password_hash, is_active) VALUES
('PHV-2025-001', 'juan.delacruz', '$2y$12$8Xzv5n0VFWLhSYCz.OAklu5DH4i8NXCPq.3cYYoa3UjwcZt1nF9ka', 1),
('PHV-2025-002', 'maria.santos', '$2y$12$8Xzv5n0VFWLhSYCz.OAklu5DH4i8NXCPq.3cYYoa3UjwcZt1nF9ka', 1),
('PHV-2025-003', 'jose.reyes', '$2y$12$8Xzv5n0VFWLhSYCz.OAklu5DH4i8NXCPq.3cYYoa3UjwcZt1nF9ka', 1);

INSERT INTO positions (position_name, description, max_votes, display_order) VALUES
('President', 'Highest executive position in the simulated national election.', 1, 1),
('Vice President', 'Second-highest executive position in the simulated national election.', 1, 2),
('Senator', 'National legislative position. Configure max votes based on your simulation rules.', 12, 3),
('Governor', 'Provincial executive position.', 1, 4),
('Mayor', 'Local city or municipal executive position.', 1, 5);

INSERT INTO candidates (full_name, political_party, position_id, photo, platform) VALUES
('Alicia Manalo', 'Partido Pag-asa', 1, NULL, 'Digital access, accountable public service, and youth-centered employment programs.'),
('Roberto Lim', 'Bayanihan Alliance', 1, NULL, 'Transparent budgeting, local livelihood support, and disaster-resilient communities.'),
('Carmen Villanueva', 'Partido Pag-asa', 2, NULL, 'Education modernization, scholarship expansion, and student mental wellness initiatives.'),
('Daniel Mercado', 'Bayanihan Alliance', 2, NULL, 'Agriculture technology, MSME incentives, and public transport improvements.'),
('Elena Cruz', 'Independent', 3, NULL, 'Ethics reform, anti-corruption reporting systems, and citizen participation.'),
('Paolo Navarro', 'Partido Pag-asa', 3, NULL, 'Affordable healthcare, regional hospitals, and digital health records.'),
('Teresa Aquino', 'Bayanihan Alliance', 4, NULL, 'Province-wide livelihood training and resilient infrastructure planning.'),
('Ramon Flores', 'Independent', 5, NULL, 'Clean governance, faster permit processing, and safer public spaces.');

INSERT INTO elections (election_title, start_date, end_date, status) VALUES
('2025 National and Local Elections Simulation', '2025-05-12 07:00:00', '2025-05-12 19:00:00', 'open');

INSERT INTO votes (election_id, voter_id, candidate_id, position_id) VALUES
(1, 'PHV-2025-001', 1, 1),
(1, 'PHV-2025-001', 3, 2),
(1, 'PHV-2025-001', 5, 3),
(1, 'PHV-2025-002', 2, 1),
(1, 'PHV-2025-002', 3, 2),
(1, 'PHV-2025-002', 6, 3),
(1, 'PHV-2025-003', 1, 1),
(1, 'PHV-2025-003', 4, 2),
(1, 'PHV-2025-003', 5, 3);

INSERT INTO audit_logs (admin_id, admin_name, action) VALUES
(1, 'System Administrator', 'Imported initial master list of eligible voters'),
(1, 'System Administrator', 'Created sample voter accounts'),
(1, 'System Administrator', 'Added initial candidates and positions'),
(1, 'System Administrator', 'Created sample election and test votes');
