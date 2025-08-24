<?php
require_once("includes/db_utils.php");
require_once("classes/League.php");
require_once("classes/Round.php");
require_once("classes/GameMatch.php");

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$db = new DBUtils();
$user = $_SESSION['user'];

// Dummy test objekti (bez baze)
$testLeague = new League();
$testLeague->setNaziv("Test Liga");
$testLeague->setGodina(2025);
$testLeague->setOpis("Ovo je test liga.");
$testLeague->setAdminId($user['id']);

$testRound = new Round();
$testRound->setLeagueId(1);
$testRound->setBrojKola(1);
$testRound->setDatum("2025-08-25");

$testMatch = new GameMatch();
$testMatch->setRoundId(1);
$testMatch->setTeam1(1, 2);
$testMatch->setTeam2(3, 4);
$testMatch->setScore(21, 15);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Test</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include("includes/header.php") ?>

    <div id="main">
        <h2>
            <?php echo $user['uloga'] == 'm' ? "Dobrodošao, " : "Dobrodošla, ";
            echo htmlspecialchars($user['ime']); ?>!
        </h2>
        <p>Uloga: <?= htmlspecialchars($user['uloga']) ?></p>
        <a href="logout.php">Odjavi se</a>

        <h3>Test prikaz klasa</h3>
        <div class="test-section">
            <h4>League</h4>
            <?= $testLeague->getHtml() ?>

            <h4>Round</h4>
            <?= $testRound->getHtml() ?>

            <h4>Match</h4>
            <?= $testMatch->getHtml() ?>
        </div>
    </div>

    <?php include("includes/footer.html") ?>
</body>
</html>