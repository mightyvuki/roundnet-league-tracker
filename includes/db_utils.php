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

}
?>