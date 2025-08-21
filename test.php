<?php
require_once("includes/db_utils.php");

$db = new DBUtils();

// ----------- USERS TEST -----------
echo "--- USERS TEST ---\n";

// Insert user
$userInserted = $db->insertUser("vukota", "Vukota", "Markišić", "vukota@example.com", "tajnapass", "admin", null, "m");
echo "User inserted: " . ($userInserted ? "YES" : "NO") . "\n";

// Get user by username (via checkLogin)
$user = $db->checkLogin("vukota", "tajnapass");
print_r($user);

// Update user
$userUpdated = $db->updateUser($user['id'], "vukota123", "Vukota", "Markišić", "vukota123@example.com", "admin", "m");
echo "User updated: " . ($userUpdated ? "YES" : "NO") . "\n";

// Delete user
$userDeleted = $db->deleteUser($user['id']);
echo "User deleted: " . ($userDeleted ? "YES" : "NO") . "\n";

// ----------- LEAGUES TEST -----------
echo "\n--- LEAGUES TEST ---\n";

// Insert admin first
$db->insertUser("admin1", "Admin", "Prvi", "admin1@example.com", "adminpass", "admin", null, "m");
$admin = $db->checkLogin("admin1", "adminpass");

// Insert league
$leagueInserted = $db->insertLeague("Summer League", 2025, "Opis lige", $admin['id']);
echo "League inserted: " . ($leagueInserted ? "YES" : "NO") . "\n";

// Get league
$leagues = $db->getLeaguesByAdmin($admin['id']);
$league = $leagues[0] ?? null;
print_r($league);

// Update league
$leagueUpdated = $db->updateLeague($league['id'], "Summer League Updated", 2025, "Novi opis", $admin['id']);
echo "League updated: " . ($leagueUpdated ? "YES" : "NO") . "\n";

// ----------- ROUNDS TEST -----------
echo "\n--- ROUNDS TEST ---\n";

// Insert round
$roundInserted = $db->insertRound($league['id'], 1, "2025-08-25");
echo "Round inserted: " . ($roundInserted ? "YES" : "NO") . "\n";

// Get round
$rounds = $db->getRoundsByLeague($league['id']);
$round = $rounds[0] ?? null;
print_r($round);

// Update round
$roundUpdated = $db->updateRound($round['id'], 2, "2025-08-26");
echo "Round updated: " . ($roundUpdated ? "YES" : "NO") . "\n";

// ----------- MATCHES TEST -----------
echo "\n--- MATCHES TEST ---\n";

// Insert players for match
$db->insertUser("player1", "Player", "One", "p1@example.com", "pass1", "user", null, "m");
$db->insertUser("player2", "Player", "Two", "p2@example.com", "pass2", "user", null, "m");
$db->insertUser("player3", "Player", "Three", "p3@example.com", "pass3", "user", null, "m");
$db->insertUser("player4", "Player", "Four", "p4@example.com", "pass4", "user", null, "m");

// Get player IDs
$p1 = $db->checkLogin("player1", "pass1");
$p2 = $db->checkLogin("player2", "pass2");
$p3 = $db->checkLogin("player3", "pass3");
$p4 = $db->checkLogin("player4", "pass4");

// Insert match
$matchInserted = $db->insertMatch($round['id'], $p1['id'], $p2['id'], $p3['id'], $p4['id'], 21, 15);
echo "Match inserted: " . ($matchInserted ? "YES" : "NO") . "\n";

// Get match
$matches = $db->getMatchesByRound($round['id']);
$match = $matches[0] ?? null;
print_r($match);

// Update match score
$matchUpdated = $db->updateMatchScore($match['id'], 21, 19);
echo "Match score updated: " . ($matchUpdated ? "YES" : "NO") . "\n";

// Delete match
$matchDeleted = $db->deleteMatch($match['id']);
echo "Match deleted: " . ($matchDeleted ? "YES" : "NO") . "\n";
?>