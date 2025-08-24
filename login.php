<?php
require_once("includes/db_utils.php");
session_start();

$db = new DBUtils();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = $db->checkLogin($username, $password);
    if ($user) {
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Pogrešno korisničko ime ili lozinka.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include("includes/header.php")?>
    
    <div id="main">
        <h2>Prijava</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <label>Korisničko ime:</label>
            <input type="text" name="username" required><br>
            <label>Lozinka:</label>
            <input type="password" name="password" required><br>
            <button type="submit">Prijavi se</button>
        </form>
        <p>Nemate nalog? <a href="register.php">Registrujte se</a></p>
    </div>

    <?php include("includes/footer.html")?>
</body>
</html>