<?php
// database_connection.php
function getDatabaseConnection() {
    $databaseFile = 'mydatabase.db';
    $db = new SQLite3($databaseFile);
    if (!$db) {
        die("Connection to database failed: " . $db->lastErrorMsg());
    }
    return $db;
}
?>
