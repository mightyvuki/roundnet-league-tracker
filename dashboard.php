<?php
    require_once("includes/db_utils.php");
    require_once("classes/League.php");
    require_once("classes/Round.php");
    require_once("classes/GameMatch.php");
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    $db = new DBUtils();
    $user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include("includes/header.php") ?>

<div id="main">
    <h2>
        <?= ($user['gender'] === 'm' ? "Dobrodošao, " : "Dobrodošla, ") . htmlspecialchars($user['ime']); ?>!
    </h2>

    <p>Uloga: <?= htmlspecialchars($user['uloga']) ?></p>
    <a href="logout.php">Odjavi se</a>
    <?php if ($user['uloga'] === 'admin'): ?>
    <hr>
        <h3>Moje lige</h3>
    <?php
        if (isset($_GET['delete_league'])) {
            $db->deleteLeague($_GET['delete_league']);
            header("Location: dashboard.php");
            exit();
        }

        $leagues = $db->getLeaguesByAdmin($user['id']);
        if (empty($leagues)) {
            echo "<p>Niste kreirali nijednu ligu.</p>";
        } else {
            echo '<div class="cards-container">';
            foreach ($leagues as $leagueData) {
                $league = new League($leagueData['id']);
                ?>
                <div class="league-card">
                    <h3><?= htmlspecialchars($league->getNaziv()) . " ({$league->getGodina()})" ?></h3>
                    <p><?= htmlspecialchars($league->getOpis()) ?></p>
                    <a id="viewLeague" href="edit_league.php?id=<?= $league->getId() ?>">Uredi ligu</a> <br><br><br>
                    <a id="viewLeague" class="del" href="?delete_league=<?= $league->getId() ?>" 
                    onclick="return confirm('Da li ste sigurni da želite obrisati ligu?')">
                    Obriši ligu
                    </a>
                </div>
                <?php
            }
            echo '</div>';
        }

        if (isset($_POST['add_league'])) {
            $naziv = $_POST['naziv'];
            $godina = $_POST['godina'];
            $opis = $_POST['opis'];

            $db->insertLeague($naziv, $godina, $opis, $user['id']);
            header("Location: dashboard.php");
            exit();
        }
        ?>
        <br><hr>
        <h3>Dodaj novu ligu</h3>
        <form method="post">
            <label for="naziv">Naziv:</label>
            <input type="text" name="naziv" id="naziv" required>

            <label for="godina">Godina:</label>
            <input type="number" name="godina" id="godina" min="2022" max="2030" required>

            <label for="opis">Opis:</label>
            <textarea name="opis" id="opis"></textarea>

            <button type="submit" name="add_league">Dodaj ligu</button>
        </form><br><br>
    <?php else: ?>
        <br><hr>
        <h2>Lige u kojima učestvujete:</h2>
        <?php
        $allLeagues = $db->getAllLeagues();
        $userLeagues = [];

        foreach ($allLeagues as $leagueData) {
            $league = new League($leagueData['id']);
            $rounds = $league->getRounds();
            $hasMatch = false;

            foreach ($rounds as $round) {
                $matches = $round->getMatches();
                foreach ($matches as $match) {
                    if (in_array($user['id'], $match->getTeam1()) || in_array($user['id'], $match->getTeam2())) {
                        $hasMatch = true;
                        break 2;
                    }
                }
            }

            if ($hasMatch) {
                $userLeagues[] = $league;
            }
        }

        if (empty($userLeagues)) {
            echo "<p>Trenutno ne učestvujete ni u jednoj ligi.</p>";
        } else {
            foreach ($userLeagues as $league) {
                echo "<br><hr>";
                echo "<h4>" . htmlspecialchars($league->getNaziv()) . " ({$league->getGodina()})</h4>";
                $rounds = $league->getRounds();
                foreach ($rounds as $round) {
                    $matches = $round->getMatches();
                    $userMatches = [];
                    foreach ($matches as $match) {
                        if (in_array($user['id'], $match->getTeam1()) || in_array($user['id'], $match->getTeam2())) {
                            $userMatches[] = $match;
                        }
                    }
                    if ($userMatches) {
                        echo "<h5>Kolo " . $round->getBrojKola() . " - " . htmlspecialchars($round->getDatum()) . "</h5>";
                        echo "<table border='1' cellpadding='5' cellspacing='0' class='tabela'>";
                        echo "<tr><th>Tim 1</th><th>Rezultat</th><th>Tim 2</th></tr>";
                        foreach ($userMatches as $match) {
                            $t1_players = array_map(function($uid) use ($db) {
                                $u = $db->getUserById($uid);
                                return $u['ime'] . " " . strtoupper(substr($u['prezime'],0,1)) . ".";
                            }, $match->getTeam1());

                            $t2_players = array_map(function($uid) use ($db) {
                                $u = $db->getUserById($uid);
                                return $u['ime'] . " " . strtoupper(substr($u['prezime'],0,1)) . ".";
                            }, $match->getTeam2());

                            echo "<tr>
                                    <td class='tim'>" . implode(" & ", $t1_players) . "</td>
                                    <td class='rezultat'>{$match->getScoreTeam1()} : {$match->getScoreTeam2()}</td>
                                    <td class='tim'>" . implode(" & ", $t2_players) . "</td>
                                  </tr>";
                        }
                        echo "</table>";
                    }
                }
                
            }
        }
        ?>
    <?php endif; ?>
</div>

<?php include("includes/footer.html") ?>
</body>
</html>