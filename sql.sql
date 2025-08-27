CREATE SCHEMA IF NOT EXISTS roundnet_league_test;
USE roundnet_league_test;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(25) NOT NULL UNIQUE,
    ime VARCHAR(20) NOT NULL,
    prezime VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    uloga ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    profile_pic VARCHAR(255),
    gender ENUM('m', 'z') NOT NULL
);

CREATE TABLE leagues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(100) NOT NULL,
    godina YEAR NOT NULL,
    opis TEXT,
    admin_id INT,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    league_id INT NOT NULL,
    broj_kola INT NOT NULL,
    datum DATE,
    FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE
);

CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    round_id INT NOT NULL,
    team1_player1_id INT NOT NULL,
    team1_player2_id INT NOT NULL,
    team2_player1_id INT NOT NULL,
    team2_player2_id INT NOT NULL,
    score_team1 INT DEFAULT 0,
    score_team2 INT DEFAULT 0,
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE,
    FOREIGN KEY (team1_player1_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team1_player2_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team2_player1_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team2_player2_id) REFERENCES users(id) ON DELETE CASCADE
);

-- podaci za testiranje (lozinka svima je 1111)
INSERT INTO users (username, ime, prezime, email, password, uloga, profile_pic, gender) VALUES
('admin1', 'Vukota', 'Markisic', 'admin1@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'admin', 'images/avatar_male.png', 'm'),
('admin2', 'Ana', 'Ivanek', 'admin2@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'admin', 'images/avatar_female.png', 'z'),
('admin3', 'Drin', 'Ferataj', 'admin3@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'admin', 'images/avatar_male.png', 'm');

INSERT INTO users (username, ime, prezime, email, password, uloga, profile_pic, gender) VALUES
('nestor', 'Nestor', 'Najbolji', 'nestor@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_male.png', 'm'),
('ana', 'Ana', 'Anić', 'ana@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_female.png', 'z'),
('stefan', 'Stefan', 'Stanković', 'stefan@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_male.png', 'm'),
('ivana', 'Ivana', 'Ivić', 'ivana@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_female.png', 'z'),
('marko', 'Marko', 'Milinković', 'marko@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_male.png', 'm'),
('sofija', 'Sofija', 'Savić', 'sofija@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_female.png', 'z'),
('dusan', 'Dušan', 'Dukić', 'dusan@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_male.png', 'm'),
('marija', 'Marija', 'Marić', 'marija@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_female.png', 'z'),
('aleksandar', 'Aleksandar', 'Aleksić', 'aleksandar@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_male.png', 'm'),
('jelena', 'Jelena', 'Jelić', 'jelena@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_female.png', 'z'),
('nikola', 'Nikola', 'Nikolić', 'nikola@example.com', '$2y$10$WquH4Xgc0byVgvbQPsZAOeCYlMO3tv8LsaqpM32I9lsAiKSJp8ICW', 'user', 'images/avatar_male.png', 'm');

INSERT INTO leagues (naziv, godina, opis, admin_id) VALUES
('Roundnet Summer League 2025', 2025, 'Prva liga u Lovćencu', 1),
('Liga Jesen 2023', 2023, 'Jeseni turnir', 2),
('Liga Proleće 2024', 2024, 'Nova sezona sa više kola', 3);

INSERT INTO rounds (league_id, broj_kola, datum) VALUES
(1, 1, '2025-07-01'),
(1, 2, '2025-07-15');

INSERT INTO rounds (league_id, broj_kola, datum) VALUES
(2, 1, '2023-09-10');

INSERT INTO rounds (league_id, broj_kola, datum) VALUES
(3, 1, '2024-03-05'),
(3, 2, '2024-03-20');

INSERT INTO matches (round_id, team1_player1_id, team1_player2_id, team2_player1_id, team2_player2_id, score_team1, score_team2) VALUES
(1, 4, 5, 1, 2, 21, 15),
(1, 6, 7, 8, 9, 10, 21),
(1, 10, 11, 3, 12, 21, 18),
(1, 1, 2, 4, 5, 14, 21),
(2, 4, 1, 6, 7, 21, 13),
(2, 4, 5, 8, 9, 21, 11),
(2, 4, 10, 2, 11, 21, 9),
(2, 4, 12, 3, 6, 21, 15),
(3, 2, 3, 4, 5, 15, 21),
(3, 6, 7, 8, 9, 21, 18),
(3, 10, 11, 1, 12, 21, 19),
(3, 4, 6, 2, 8, 21, 14),
(4, 4, 2, 6, 7, 21, 17),
(4, 4, 3, 8, 9, 21, 15),
(5, 4, 5, 10, 11, 21, 12),
(5, 4, 6, 1, 12, 21, 14);