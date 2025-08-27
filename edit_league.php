<?php
    require_once("includes/db_utils.php");
    require_once("classes/League.php");
    require_once("classes/Round.php");
    require_once("classes/GameMatch.php");
    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']['uloga'] !== 'admin') {
        header("Location: index.php");
        exit();
    }

    $db = new DBUtils();
    if (!isset($_GET['id'])) {
        header("Location: leagues.php");
        exit();
    }

    $league = new League($_GET['id']);
    $rounds = $league->getRounds();

    if (isset($_POST['add_round'])) {
        $broj_kola = $_POST['broj_kola'];
        $datum = $_POST['datum'];
        $db->insertRound($league->getId(), $broj_kola, $datum);
        header("Location: edit_league.php?id=" . $league->getId());
        exit();
    }
    if (isset($_GET['delete_round'])) {
        $db->deleteRound($_GET['delete_round']);
        header("Location: edit_league.php?id=" . $league->getId());
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Uredi ligu: <?= htmlspecialchars($league->getNaziv()) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include("includes/header.php"); ?>

    <div id="main">
        <h2 class="naslov-lige"><?= htmlspecialchars($league->getNaziv()) ?> (<?= $league->getGodina() ?>)</h2>
        <p><?= htmlspecialchars($league->getOpis()) ?></p>
        <br><hr>
        <h3>Kola</h3>
        <?php if (empty($rounds)): ?>
            <p class="no-leagues">Nema kola u ovoj ligi.</p>
        <?php else: ?>
            <div class="cards-container">
            <?php foreach ($rounds as $round): ?>
                <?php $datum = (new DateTime($round->getDatum()))->format('j.n.Y'); ?>
                <div class="league-card">
                    <h3>Kolo <?= $round->getBrojKola() ?></h3>
                    <p><?= $datum ?></p>
                    <a id="viewLeague" href="edit_round.php?id=<?= $round->getId() ?>">Uredi mečeve</a> <br><br><br>
                    <a id="viewLeague" class="del" href="?id=<?= $league->getId() ?>&delete_round=<?= $round->getId() ?>" onclick="return confirm('Obrisati kolo?')">Obriši kolo</a>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <br><hr>
        <h3>Dodaj novo kolo</h3>
        <form method="post">
            <label for="broj_kola">Broj kola:</label>
            <input type="number" name="broj_kola" id="broj_kola" required>

            <label for="datum">Datum:</label>
            <input type="date" name="datum" id="datum" required>

            <button type="submit" name="add_round">Dodaj kolo</button>
        </form>
    </div>
    <br>
    <?php include("includes/footer.html"); ?>
</body>
</html>