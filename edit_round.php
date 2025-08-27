<?php
    require_once("includes/db_utils.php");
    require_once("classes/Round.php");
    require_once("classes/GameMatch.php");
    require_once("classes/League.php");
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

    $round = new Round($_GET['id']);
    $matches = $round->getMatches();
    $roundId = $round->getId();
    $league = new League($round->getLeagueId());
    $allUsers = $db->getAllUsers(); 

    if (isset($_POST['add_match'])) {
        $t1p1 = (int)$_POST['team1_player1'];
        $t1p2 = (int)$_POST['team1_player2'];
        $t2p1 = (int)$_POST['team2_player1'];
        $t2p2 = (int)$_POST['team2_player2'];
        $score1 = max(0, min(21, (int)$_POST['score1']));
        $score2 = max(0, min(21, (int)$_POST['score2']));

        $db->insertMatch($roundId, $t1p1, $t1p2, $t2p1, $t2p2, $score1, $score2);
        header("Location: edit_round.php?id=$roundId");
        exit();
    }

    if (isset($_POST['update_match'])) {
        $matchId = (int)$_POST['match_id'];
        $score1 = max(0, min(21, (int)$_POST['score1']));
        $score2 = max(0, min(21, (int)$_POST['score2']));

        $db->updateMatchScore($matchId, $score1, $score2);
        header("Location: edit_round.php?id=$roundId");
        exit();
    }

    if (isset($_GET['delete_match'])) {
        $matchId = (int)$_GET['delete_match'];
        $db->deleteMatch($matchId);
        header("Location: edit_round.php?id=$roundId");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Kolo <?= htmlspecialchars($round->getBrojKola()) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include("includes/header.php"); ?>

    <div id="main">
        <h2 class="naslov-lige"><?= htmlspecialchars($league->getNaziv()) ?> (<?= $league->getGodina() ?>)</h2>
        <h3>Kolo <?= $round->getBrojKola() ?> - <?= (new DateTime($round->getDatum()))->format('j.n.Y') ?></h3>

        <?php if (empty($matches)): ?>
            <p>Nema mečeva u ovom kolu.</p>
        <?php else: ?>
            <table class="tabela">
                <tr >
                    <th>Tim 1</th>
                    <th>Rezultat</th>
                    <th>Tim 2</th>
                    <th>Akcije</th>
                </tr>
                <?php foreach ($matches as $match): ?>
                <?php
                    $t1 = [];
                    foreach ($match->getTeam1() as $uid) {
                        $u = $db->getUserById($uid);
                        $t1[] = htmlspecialchars($u['ime'] . " " . strtoupper(substr($u['prezime'], 0, 1)) . ".");
                    }

                    $t2 = [];
                    foreach ($match->getTeam2() as $uid) {
                        $u = $db->getUserById($uid);
                        $t2[] = htmlspecialchars($u['ime'] . " " . strtoupper(substr($u['prezime'], 0, 1)) . ".");
                    }

                    $team1_str = implode(" & ", $t1);
                    $team2_str = implode(" & ", $t2);
                ?>
                <tr>
                    <td class="tim"><?= $team1_str ?></td>
                    <form method="post" class="rezultat-forma">
                    <td class="rezultat-td">
                        <input type="hidden" name="match_id" value="<?= (int)$match->getId() ?>">
                        <input type="number" name="score1" value="<?= (int)$match->getScoreTeam1() ?>" min="0" max="21" required>
                        :
                        <input type="number" name="score2" value="<?= (int)$match->getScoreTeam2() ?>" min="0" max="21" required>
                    </td>
                    <td class="tim"><?= $team2_str ?></td>
                    <td>
                        <button type="submit" name="update_match" class="update_match">Sačuvaj</button>
                        <a href="?id=<?= $roundId ?>&delete_match=<?= (int)$match->getId() ?>" onclick="return confirm('Obrisati meč?')">Obriši</a>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <h4>Dodaj novi meč</h4>
        <form method="post" id="addMatchForm">
            Tim 1: 
            <select name="team1_player1" id="t1p1" required>
                <option value="" disabled selected hidden>Izaberi</option>
                <?php foreach ($allUsers as $user): ?>
                    <option value="<?= (int)$user['id'] ?>"><?= htmlspecialchars($user['ime'] . " " . $user['prezime']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="team1_player2" id="t1p2" required>
                <option value="" disabled selected hidden>Izaberi</option>
                <?php foreach ($allUsers as $user): ?>
                    <option value="<?= (int)$user['id'] ?>"><?= htmlspecialchars($user['ime'] . " " . $user['prezime']) ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            Tim 2: 
            <select name="team2_player1" id="t2p1" required>
                <option value="" disabled selected hidden>Izaberi</option>
                <?php foreach ($allUsers as $user): ?>
                    <option value="<?= (int)$user['id'] ?>"><?= htmlspecialchars($user['ime'] . " " . $user['prezime']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="team2_player2" id="t2p2" required>
                <option value="" disabled selected hidden>Izaberi</option>
                <?php foreach ($allUsers as $user): ?>
                    <option value="<?= (int)$user['id'] ?>"><?= htmlspecialchars($user['ime'] . " " . $user['prezime']) ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            Rezultat: 
            <input type="number" name="score1" id="score1" value="0" style="width:50px" min="0" max="21" required> :
            <input type="number" name="score2" id="score2" value="0" style="width:50px" min="0" max="21" required>
            <br>
            <button type="submit" name="add_match">Dodaj meč</button>
        </form>

        <script>
        const t1p1 = document.getElementById("t1p1");
        const t1p2 = document.getElementById("t1p2");
        const t2p1 = document.getElementById("t2p1");
        const t2p2 = document.getElementById("t2p2");

        const selects = [t1p1, t1p2, t2p1, t2p2];

        function updateAllOptions() {
            const values = selects.map(s => s.value);
            selects.forEach(select => {
                for (let option of select.options) {
                    option.disabled = values.includes(option.value) && option.value !== select.value && option.value !== "";
                }
            });
        }

        selects.forEach(select => {
            select.addEventListener("change", updateAllOptions);
        });
        updateAllOptions();
        </script>
    </div>

    <?php include("includes/footer.html"); ?>
</body>
</html>
