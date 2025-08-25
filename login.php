<?php
    require_once("includes/db_utils.php");
    session_start();

    $db = new DBUtils();
    $error = "";

    if (isset($_COOKIE['remember_me']) && !isset($_SESSION['user'])) {
        $userId = intval($_COOKIE['remember_me']);
        $user = $db->getUserById($userId);
        if ($user) {
            $_SESSION['user'] = $user;
            header("Location: dashboard.php");
            exit();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        if (!preg_match("/^[a-zA-Z0-9_]{4,25}$/", $username)) {
            $error = "Korisničko ime može sadržati samo slova, brojeve i donje crte (4-25 karaktera).";
        } else {
            $password = $_POST['password'];
            $remember = isset($_POST['remember']);

            $user = $db->checkLogin($username, $password);
            if ($user) {
                $_SESSION['user'] = $user;

                if ($remember) {
                    setcookie("remember_me", $user['id'], time() + (86400*30), "/"); 
                } else {
                    if (isset($_COOKIE['remember_me'])) {
                        setcookie("remember_me", "", time() - 3600, "/");
                    }
                }

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Pogrešno korisničko ime ili lozinka.";
            }
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
        <?php if ($error) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
        <form method="post">
            <label>Korisničko ime:</label>
            <input type="text" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required><br>

            <label>Lozinka:</label>
            <input type="password" name="password" required><br>

            <label id="remember-me"><input type="checkbox" name="remember">Zapamti me</label><br>

            <button type="submit">Prijavi se</button>
        </form>
        <p>Nemate nalog? <a href="register.php">Registrujte se</a></p>
    </div>

    <?php include("includes/footer.html")?>
</body>
</html>