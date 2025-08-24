<?php
    require_once("includes/db_utils.php");
    session_start();

    $db = new DBUtils();
    $error = "";
    $success = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $ime = trim($_POST['ime']);
        $prezime = trim($_POST['prezime']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $gender = $_POST['gender'];

        if (!preg_match('/^[a-zA-Z0-9_]{4,25}$/', $username)) {
            $error = "Korisničko ime može sadržati samo slova, brojeve i donje crte (4-25 karaktera).";
        } 
        elseif (!preg_match('/^[a-zA-ZčćžšđČĆŽŠĐ\s\-]{2,20}$/u', $ime)) {
            $error = "Ime može sadržati samo slova, razmake i crte (2-20 karaktera).";
        } 
        elseif (!preg_match('/^[a-zA-ZčćžšđČĆŽŠĐ\s\-]{2,30}$/u', $prezime)) {
            $error = "Prezime može sadržati samo slova, razmake i crte (2-30 karaktera).";
        } 
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Nevažeća email adresa.";
        } 
        elseif (!in_array($gender, ['m','z'])) {
            $error = "Nevažeći pol.";
        } 
        else {
            $insert = $db->insertUser($username, $ime, $prezime, $email, $password, "user", null, $gender);
            if ($insert) {
                $success = "Uspešno ste se registrovali! <a href='index.php'>Prijavite se</a>";
            } else {
                $error = "Korisničko ime ili email već postoji.";
            }
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
    <?php include("includes/header.php")?>
    
    <div id="main">
        <h2>Registracija</h2>
        <?php 
            if ($error) echo "<p style='color:var(--accent);'>" . htmlspecialchars($error) . "</p>"; 
            if ($success) echo "<p style='color:var(--text);'>" . $success . "</p>"; 
        ?>
        <form method="POST">
            <label>Korisničko ime:</label>
            <input type="text" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required><br>

            <label>Ime:</label>
            <input type="text" name="ime" value="<?= isset($_POST['ime']) ? htmlspecialchars($_POST['ime']) : '' ?>" required><br>

            <label>Prezime:</label>
            <input type="text" name="prezime" value="<?= isset($_POST['prezime']) ? htmlspecialchars($_POST['prezime']) : '' ?>" required><br>

            <label>Email:</label>
            <input type="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required><br>

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
    </div>
    
    <?php include("includes/footer.html")?>
</body>
</html>