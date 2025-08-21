<?php
require_once("includes/db_utils.php");
session_start();

// Provera da li je korisnik ulogovan
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$db = new DBUtils();
$user = $_SESSION['user'];

// Dobavljanje liga koje korisnik administrira
$leagues = $db->getLeaguesByAdmin($user['id']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Dobrodošao, <?php echo htmlspecialchars($user['ime']); ?>!</h2>
    <p>Uloga: <?php echo htmlspecialchars($user['uloga']); ?></p>
    <a href="logout.php">Odjavi se</a>

    <h3>Moje lige</h3>
    <?php if (empty($leagues)) : ?>
        <p>Nemate nijednu ligu.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($leagues as $league) : ?>
                <li>
                    <?php echo htmlspecialchars($league['naziv']); ?> (<?php echo $league['godina']; ?>)
                    - <a href="view_league.php?id=<?php echo $league['id']; ?>">Prikaži</a>
                    - <a href="edit_league.php?id=<?php echo $league['id']; ?>">Izmeni</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="add_league.php">Dodaj novu ligu</a></p>
</body>
</html>