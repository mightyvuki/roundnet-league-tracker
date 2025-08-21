<?php
require_once("includes/db_utils.php");
session_start();

$db = new DBUtils();
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $ime = $_POST['ime'];
    $prezime = $_POST['prezime'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];

    $insert = $db->insertUser($username, $ime, $prezime, $email, $password, "user", null, $gender);

    if ($insert) {
        $success = "Uspešno ste se registrovali! <a href='index.php'>Prijavite se</a>";
    } else {
        $error = "Korisničko ime ili email već postoji.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registracija</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Registracija</h2>
    <?php 
        if ($error) echo "<p style='color:red;'>$error</p>"; 
        if ($success) echo "<p style='color:green;'>$success</p>"; 
    ?>
    <form method="POST">
        <label>Korisničko ime:</label>
        <input type="text" name="username" required><br>
        <label>Ime:</label>
        <input type="text" name="ime" required><br>
        <label>Prezime:</label>
        <input type="text" name="prezime" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Lozinka:</label>
        <input type="password" name="password" required><br>
        <label>Pol:</label>
        <select name="gender" required>
            <option value="m">Muški</option>
            <option value="z">Ženski</option>
        </select><br>
        <button type="submit">Registruj se</button>
    </form>
    <p>Već imate nalog? <a href="index.php">Prijavite se</a></p>
</body>
</html>