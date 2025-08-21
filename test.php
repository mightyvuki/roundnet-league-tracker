<?php
require_once("includes/constants.php");
require_once("includes/db_utils.php");

$db = new DBUtils();
/*
// 1. Test insertUser
echo "=== Insert User === <br>";
$result = $db->insertUser("vukota", "Vukota", "Markisic", "vukota@test.com", "123456", "user", null, "m");
if ($result) {
    echo "<br> User inserted successfully.<br>";
} else {
    echo "<br> Failed to insert user (username/email already exists).<br>";
}

// 2. Test checkLogin
echo "=== Check Login ===<br>";
$user = $db->checkLogin("vukota", "123456");
if ($user) {
    echo "Login successful for: " . $user["username"] . "<br>";
} else {
    echo "Login failed.<br>";
}
*/
//3. Test getUserByEmail
echo "=== Get User by Email === <br>";
$userByEmail = $db->getUserByEmail("vukota_updated@test.com");
if ($userByEmail) {
    echo "User found: " . $userByEmail["ime"] . "<br>";
} else {
    echo "User not found.<br>";
}
/*
// 4. Test updateUser
echo "=== Update User ===<br>";
if ($userByEmail) {
    $updateResult = $db->updateUser(
        $userByEmail["id"],
        "vukota_updated",
        "Vukota",
        "Markisic",
        "vukota_updated@test.com",
        "user",
        "m"
    );
    echo $updateResult ? "User updated successfully.<br>" : "Failed to update user.<br>";
}
*/
// 5. Test deleteUser
echo "=== Delete User ===<br>";
if ($userByEmail) {
    $deleteResult = $db->deleteUser($userByEmail["id"]);
    echo $deleteResult ? "User deleted successfully.<br>" : "Failed to delete user.<br>";
}
?>