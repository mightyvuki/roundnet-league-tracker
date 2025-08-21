<?php
require_once("constants.php");

class DBUtils {
    private $conn;
    private $hashing_salt = "~?nocasserastajemo024!@#";

    public function __construct($configFile = "config.ini") {
        if ($config = parse_ini_file($configFile)) {
            $host = $config["host"];
            $database = $config["database"];
            $user = $config["user"];
            $password = $config["password"];
            $this->conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public function insertUser($username, $ime, $prezime, $email, $password, $uloga = "user", $profile_pic = null, $gender) {
        try {
            $sql_check = "SELECT * FROM " . TBL_USERS . " WHERE " . COL_USER_USERNAME . " = :username";
            $st = $this->conn->prepare($sql_check);
            $st->bindValue(":username", $username);
            $st->execute();
            if ($st->fetch()) return false; // username vec postoji

            $hashed_password = crypt($password, $this->hashing_salt);

            if (!$profile_pic) {
                $profile_pic = $gender === "z" ? "images/avatar_female.png" : "images/avatar_male.png";
            }

            $sql_insert = "INSERT INTO " . TBL_USERS . " (".COL_USER_USERNAME.", ".COL_USER_IME.", ".COL_USER_PREZIME.", ".COL_USER_EMAIL.", ".COL_USER_PASSWORD.", ".COL_USER_ULOGA.", ".COL_USER_PROFILE_PIC.", ".COL_USER_GENDER.") 
                           VALUES (:username, :ime, :prezime, :email, :password, :uloga, :profile_pic, :gender)";
            $st = $this->conn->prepare($sql_insert);
            $st->bindValue(":username", $username);
            $st->bindValue(":ime", $ime);
            $st->bindValue(":prezime", $prezime);
            $st->bindValue(":email", $email);
            $st->bindValue(":password", $hashed_password);
            $st->bindValue(":uloga", $uloga);
            $st->bindValue(":profile_pic", $profile_pic);
            $st->bindValue(":gender", $gender);
            return $st->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function checkLogin($username, $password) {
        $hashed_password = crypt($password, $this->hashing_salt);
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . COL_USER_USERNAME . " = :username AND " . COL_USER_PASSWORD . " = :password";
        $st = $this->conn->prepare($sql);
        $st->bindValue(":username", $username);
        $st->bindValue(":password", $hashed_password);
        $st->execute();
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        try {
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . COL_USER_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->execute();
            return $st->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUserByEmail($email) {
        try {
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . COL_USER_EMAIL . " = :email";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":email", $email);
            $st->execute();
            return $st->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateUser($id, $username, $ime, $prezime, $email, $uloga, $gender, $profile_pic = null, $newPassword = null) {
        try {
            // provera unique username/email
            $sql_check = "SELECT " . COL_USER_ID . " FROM " . TBL_USERS . "
                        WHERE (" . COL_USER_USERNAME . " = :username OR " . COL_USER_EMAIL . " = :email)
                        AND " . COL_USER_ID . " <> :id";
            $st = $this->conn->prepare($sql_check);
            $st->bindValue(":username", $username, PDO::PARAM_STR);
            $st->bindValue(":email", $email, PDO::PARAM_STR);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->execute();
            if ($st->fetch()) return false;

            if ($profile_pic === null || $profile_pic === "") {
                $profile_pic = ($gender === "z") ? "images/avatar_female.png" : "images/avatar_male.png";
            }

            $params = [
                ":id"          => $id,
                ":username"    => $username,
                ":ime"         => $ime,
                ":prezime"     => $prezime,
                ":email"       => $email,
                ":uloga"       => $uloga,
                ":gender"      => $gender,
                ":profile_pic" => $profile_pic
            ];

            if ($newPassword !== null && $newPassword !== "") {
                $hashed = crypt($newPassword, $this->hashing_salt);
                $sql = "UPDATE " . TBL_USERS . " SET "
                    . COL_USER_USERNAME . " = :username, "
                    . COL_USER_IME . " = :ime, "
                    . COL_USER_PREZIME . " = :prezime, "
                    . COL_USER_EMAIL . " = :email, "
                    . COL_USER_ULOGA . " = :uloga, "
                    . COL_USER_GENDER . " = :gender, "
                    . COL_USER_PROFILE_PIC . " = :profile_pic, "
                    . COL_USER_PASSWORD . " = :password
                    WHERE " . COL_USER_ID . " = :id";
                $params[":password"] = $hashed;
            } else {
                $sql = "UPDATE " . TBL_USERS . " SET "
                    . COL_USER_USERNAME . " = :username, "
                    . COL_USER_IME . " = :ime, "
                    . COL_USER_PREZIME . " = :prezime, "
                    . COL_USER_EMAIL . " = :email, "
                    . COL_USER_ULOGA . " = :uloga, "
                    . COL_USER_GENDER . " = :gender, "
                    . COL_USER_PROFILE_PIC . " = :profile_pic
                    WHERE " . COL_USER_ID . " = :id";
            }

            $st = $this->conn->prepare($sql);
            return $st->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteUser($id) {
        try {
            $sql = "DELETE FROM " . TBL_USERS . " WHERE " . COL_USER_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->execute();
            return $st->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    // LIGE
    public function insertLeague($naziv, $godina, $opis, $admin_id) {
        try {
            $sql = "INSERT INTO " . TBL_LEAGUES . " (" 
                    . COL_LEAGUE_NAZIV . ", "
                    . COL_LEAGUE_GODINA . ", "
                    . COL_LEAGUE_OPIS . ", "
                    . COL_LEAGUE_ADMIN_ID . ") 
                    VALUES (:naziv, :godina, :opis, :admin_id)";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":naziv", $naziv);
            $st->bindValue(":godina", $godina, PDO::PARAM_INT);
            $st->bindValue(":opis", $opis);
            $st->bindValue(":admin_id", $admin_id, PDO::PARAM_INT);
            return $st->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getLeagueById($id) {
        try {
            $sql = "SELECT * FROM " . TBL_LEAGUES . " WHERE " . COL_LEAGUE_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->execute();
            return $st->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateLeague($id, $naziv, $godina, $opis, $admin_id) {
        try {
            $sql = "UPDATE " . TBL_LEAGUES . " SET "
                    . COL_LEAGUE_NAZIV . " = :naziv, "
                    . COL_LEAGUE_GODINA . " = :godina, "
                    . COL_LEAGUE_OPIS . " = :opis, "
                    . COL_LEAGUE_ADMIN_ID . " = :admin_id
                    WHERE " . COL_LEAGUE_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->bindValue(":naziv", $naziv);
            $st->bindValue(":godina", $godina, PDO::PARAM_INT);
            $st->bindValue(":opis", $opis);
            $st->bindValue(":admin_id", $admin_id, PDO::PARAM_INT);
            return $st->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteLeague($id) {
        try {
            $sql = "DELETE FROM " . TBL_LEAGUES . " WHERE " . COL_LEAGUE_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->execute();
            return $st->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getLeaguesByAdmin($admin_id) {
        try {
            $sql = "SELECT * FROM " . TBL_LEAGUES . " WHERE " . COL_LEAGUE_ADMIN_ID . " = :admin_id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":admin_id", $admin_id, PDO::PARAM_INT);
            $st->execute();
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // KOLA / RUNDE

    public function insertRound($league_id, $broj_kola, $datum) {
        try {
            $sql = "INSERT INTO " . TBL_ROUNDS . " (" 
                    . COL_ROUND_LEAGUE_ID . ", "
                    . COL_ROUND_BROJ_KOLA . ", "
                    . COL_ROUND_DATUM . ") 
                    VALUES (:league_id, :broj_kola, :datum)";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":league_id", $league_id, PDO::PARAM_INT);
            $st->bindValue(":broj_kola", $broj_kola, PDO::PARAM_INT);
            $st->bindValue(":datum", $datum);
            return $st->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getRoundById($id) {
        try {
            $sql = "SELECT * FROM " . TBL_ROUNDS . " WHERE " . COL_ROUND_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->execute();
            return $st->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getRoundsByLeague($league_id) {
        try {
            $sql = "SELECT * FROM " . TBL_ROUNDS . " WHERE " . COL_ROUND_LEAGUE_ID . " = :league_id ORDER BY " . COL_ROUND_BROJ_KOLA;
            $st = $this->conn->prepare($sql);
            $st->bindValue(":league_id", $league_id, PDO::PARAM_INT);
            $st->execute();
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateRound($id, $broj_kola, $datum) {
        try {
            $sql = "UPDATE " . TBL_ROUNDS . " SET "
                    . COL_ROUND_BROJ_KOLA . " = :broj_kola, "
                    . COL_ROUND_DATUM . " = :datum
                    WHERE " . COL_ROUND_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->bindValue(":broj_kola", $broj_kola, PDO::PARAM_INT);
            $st->bindValue(":datum", $datum);
            return $st->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteRound($id) {
        try {
            $sql = "DELETE FROM " . TBL_ROUNDS . " WHERE " . COL_ROUND_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->execute();
            return $st->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    // ********** MATCHES **********

    public function insertMatch($round_id, $team1_player1_id, $team1_player2_id, $team2_player1_id, $team2_player2_id, $score_team1 = 0, $score_team2 = 0) {
        try {
            $sql = "INSERT INTO " . TBL_MATCHES . " ("
                    . COL_MATCH_ROUND_ID . ", "
                    . COL_MATCH_TEAM1_P1 . ", "
                    . COL_MATCH_TEAM1_P2 . ", "
                    . COL_MATCH_TEAM2_P1 . ", "
                    . COL_MATCH_TEAM2_P2 . ", "
                    . COL_MATCH_SCORE1 . ", "
                    . COL_MATCH_SCORE2 . ") 
                    VALUES (:round_id, :t1p1, :t1p2, :t2p1, :t2p2, :score1, :score2)";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":round_id", $round_id, PDO::PARAM_INT);
            $st->bindValue(":t1p1", $team1_player1_id, PDO::PARAM_INT);
            $st->bindValue(":t1p2", $team1_player2_id, PDO::PARAM_INT);
            $st->bindValue(":t2p1", $team2_player1_id, PDO::PARAM_INT);
            $st->bindValue(":t2p2", $team2_player2_id, PDO::PARAM_INT);
            $st->bindValue(":score1", $score_team1, PDO::PARAM_INT);
            $st->bindValue(":score2", $score_team2, PDO::PARAM_INT);
            return $st->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getMatchById($id) {
        try {
            $sql = "SELECT * FROM " . TBL_MATCHES . " WHERE " . COL_MATCH_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->execute();
            return $st->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getMatchesByRound($round_id) {
        try {
            $sql = "SELECT * FROM " . TBL_MATCHES . " WHERE " . COL_MATCH_ROUND_ID . " = :round_id ORDER BY " . COL_MATCH_ID;
            $st = $this->conn->prepare($sql);
            $st->bindValue(":round_id", $round_id, PDO::PARAM_INT);
            $st->execute();
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateMatchScore($id, $score_team1, $score_team2) {
        try {
            $sql = "UPDATE " . TBL_MATCHES . " SET "
                    . COL_MATCH_SCORE1 . " = :score1, "
                    . COL_MATCH_SCORE2 . " = :score2
                    WHERE " . COL_MATCH_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->bindValue(":score1", $score_team1, PDO::PARAM_INT);
            $st->bindValue(":score2", $score_team2, PDO::PARAM_INT);
            return $st->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteMatch($id) {
        try {
            $sql = "DELETE FROM " . TBL_MATCHES . " WHERE " . COL_MATCH_ID . " = :id";
            $st = $this->conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $st->execute();
            return $st->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>