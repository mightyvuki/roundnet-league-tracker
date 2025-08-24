<?php
    require_once("includes/db_utils.php");
    require_once("classes/League.php");
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
        exit();
    }

    $db = new DBUtils();
    $user = $_SESSION['user'];

    $leaguesData = $db->getAllLeagues(); 
    $leagues = [];
    foreach ($leaguesData as $l) {
        $leagues[] = new League($l['id']);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lige</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include("includes/header.php") ?>

    <div id="main">
        <h2>Moje lige</h2>
        <div class="cards-container">
            <?php if (empty($leagues)): ?>
                <p>Nemate nijednu ligu.</p>
            <?php else: ?>
                <?php foreach ($leagues as $league): ?>
                    <div class="league-card">
                        <h3><?= htmlspecialchars($league->getNaziv()) ?> (<?= $league->getGodina() ?>)</h3>
                        <p><?= htmlspecialchars($league->getOpis()) ?></p>
                        <a href="view_league.php?id=<?= $league->getId() ?>" id="viewLeague">Pregledaj ligu</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include("includes/footer.html") ?>
</body>
</html>