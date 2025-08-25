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

    if (!isset($_GET['id'])) {
        header("Location: leagues.php");
        exit();
    }

    $league = new League($_GET['id']);
    $rounds = $league->getRounds();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($league->getNaziv()) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include("includes/header.php") ?>

<div id="main">
    <h2><?= htmlspecialchars($league->getNaziv()) ?> (<?= $league->getGodina() ?>)</h2>
    <p><?= htmlspecialchars($league->getOpis()) ?></p>

    <?php if (empty($rounds)): ?>
        <p>Nema kola u ovoj ligi.</p>
    <?php else: ?>
        <?php foreach ($rounds as $round): ?>
            <h3>Kolo <?= $round->getBrojKola() ?> - <?= htmlspecialchars($round->getDatum()) ?></h3>
            <?php
            $matches = $round->getMatches();
            if (empty($matches)) {
                echo "<p>Nema mečeva u ovom kolu.</p>";
            } else {
                echo "<table border='1' cellpadding='5' cellspacing='0'>";
                echo "<tr><th>Tim 1</th><th>Tim 2</th><th>Rezultat</th></tr>";
                foreach ($matches as $match) {
                    $t1_ids = $match->getTeam1();
                    $t2_ids = $match->getTeam2();

                    $t1_players = [];
                    foreach ($t1_ids as $uid) {
                        $u = $match->db->getUserById($uid);
                        $t1_players[] = $u['ime'] . " " . strtoupper(substr($u['prezime'], 0, 1)) . ".";
                    }

                    $t2_players = [];
                    foreach ($t2_ids as $uid) {
                        $u = $match->db->getUserById($uid);
                        $t2_players[] = $u['ime'] . " " . strtoupper(substr($u['prezime'], 0, 1)) . ".";
                    }

                    $team1_str = implode(" & ", $t1_players);
                    $team2_str = implode(" & ", $t2_players);

                    echo "<tr>
                            <td>{$team1_str}</td>
                            <td>{$team2_str}</td>
                            <td>{$match->getScoreTeam1()} : {$match->getScoreTeam2()}</td>
                        </tr>";
                }
                echo "</table>";
            }
            ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include("includes/footer.html") ?>
</body>
</html>