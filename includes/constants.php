<?php
define("TBL_USERS", "users");
define("COL_USER_ID", "id");
define("COL_USER_USERNAME", "username");
define("COL_USER_IME", "ime");
define("COL_USER_PREZIME", "prezime");
define("COL_USER_EMAIL", "email");
define("COL_USER_PASSWORD", "password");
define("COL_USER_ULOGA", "uloga");
define("COL_USER_PROFILE_PIC", "profile_pic");
define("COL_USER_GENDER", "gender");

define("TBL_LEAGUES", "leagues");
define("COL_LEAGUE_ID", "id");
define("COL_LEAGUE_NAZIV", "naziv");
define("COL_LEAGUE_GODINA", "godina");
define("COL_LEAGUE_OPIS", "opis");
define("COL_LEAGUE_ADMIN_ID", "admin_id");

define("TBL_ROUNDS", "rounds");
define("COL_ROUND_ID", "id");
define("COL_ROUND_LEAGUE_ID", "league_id");
define("COL_ROUND_BROJ_KOLA", "broj_kola");
define("COL_ROUND_DATUM", "datum");

define("TBL_MATCHES", "matches");
define("COL_MATCH_ID", "id");
define("COL_MATCH_ROUND_ID", "round_id");
define("COL_MATCH_TEAM1_P1", "team1_player1_id");
define("COL_MATCH_TEAM1_P2", "team1_player2_id");
define("COL_MATCH_TEAM2_P1", "team2_player1_id");
define("COL_MATCH_TEAM2_P2", "team2_player2_id");
define("COL_MATCH_SCORE1", "score_team1");
define("COL_MATCH_SCORE2", "score_team2");
?>