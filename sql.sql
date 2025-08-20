CREATE SCHEMA IF NOT EXISTS roundnet_league;
USE roundnet_league;

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
    admin_id INT NOT NULL,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
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