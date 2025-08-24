<?php
    require_once("includes/db_utils.php");
    if (session_status() === PHP_SESSION_NONE) session_start();

    $db = new DBUtils();
    $error = "";
    $success = "";

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    $user = $db->getUserById($_SESSION['user']['id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $ime = trim($_POST['ime']);
        $prezime = trim($_POST['prezime']);
        $email = trim($_POST['email']);
        $gender = $_POST['gender'];
        $newPassword = $_POST['password'] ?? null;

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
            $profile_pic = $user['profile_pic'];
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['profile_pic']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
                $allowedExt = ['jpg','jpeg','png'];

                if (!in_array($ext, $allowedExt)) {
                    $error = "Dozvoljene ekstenzije slike: jpg, jpeg, png.";
                } else {
                    $uploadDir = "uploads/";
                    $newName = $uploadDir . "avatar_{$user['id']}." . $ext;
                    if (!move_uploaded_file($tmpName, $newName)) {
                        $error = "Nije moguće sačuvati sliku.";
                    } else {
                        $profile_pic = $newName;
                    }
                }
            }

            if (!$error) {
                $update = $db->updateUser(
                    $user['id'], $username, $ime, $prezime, $email, $user['uloga'], $gender, $profile_pic, $newPassword
                );

                if ($update) {
                    $success = "Uspešno ste ažurirali profil!";
                    $_SESSION['user'] = $db->getUserById($user['id']);
                    $user = $_SESSION['user'];
                } else {
                    $error = "Korisničko ime ili email već postoji.";
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Izmena profila</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include("includes/header.php") ?>

    <div id="main">
        <h2>Izmena profila</h2>
        <?php
            if ($error) echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
            if ($success) echo "<p class='success'>" . htmlspecialchars($success) . "</p>";
        ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Korisničko ime:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label>Ime:</label>
            <input type="text" name="ime" value="<?= htmlspecialchars($user['ime']) ?>" required>

            <label>Prezime:</label>
            <input type="text" name="prezime" value="<?= htmlspecialchars($user['prezime']) ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>Lozinka (ostavi prazno ako ne menjaš):</label>
            <input type="password" name="password">

            <label>Pol:</label>
            <select name="gender" required>
                <option value="m">Muški</option>
                <option value="z">Ženski</option>
            </select>

            <label>Profilna slika:</label>
            <input type="file" name="profile_pic" accept="image/*" onchange="previewImage(event)">
            <div class="avatar-preview">
                <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profilna slika" id="avatarPreview">
            </div>

            <button type="submit">Sačuvaj izmene</button>
        </form>
    </div>

    <?php include("includes/footer.html") ?>
    <script>
    function previewImage(event) {
        const output = document.getElementById('avatarPreview');
        output.src = URL.createObjectURL(event.target.files[0]);
    }
    </script>
</body>
</html>