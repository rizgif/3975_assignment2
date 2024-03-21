<?php
$db = new SQLite3('mydatabase.db');

$user_id = $_POST['user_id'];
$access_type = $_POST['access_type'];
$access_value = isset($_POST['access_value']) ? 1 : 0;

// Update the access value in the database
$db->exec("UPDATE users SET $access_type = $access_value WHERE id = $user_id");

header('Location: admin.php');
?>