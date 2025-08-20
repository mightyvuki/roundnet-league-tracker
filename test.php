<?php
require_once("includes/db_utils.php");

$db = new DBUtils();

$username = "vkt1234";
$ime = "Vukota";
$prezime = "Markisic";
$email = "vukota@fake.com";
$password = "sifra123";
$gender = "m"; 

$inserted = $db->insertUser($username, $ime, $prezime, $email, $password, "user", null, $gender);

if ($inserted) {
    echo "Radi";
} else {
    echo "Ne radi";
}
?>