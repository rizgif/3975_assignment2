<?php
// Function to create SQLite tables
function createTables($db) {
    // Create users table
    $db->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT "user",
        can_login BOOLEAN NOT NULL DEFAULT FALSE,
        can_access_transaction BOOLEAN NOT NULL DEFAULT FALSE,
        can_access_bucket BOOLEAN NOT NULL DEFAULT FALSE,
        can_access_report BOOLEAN NOT NULL DEFAULT FALSE
    )');

    // Check for existing admin account
    $result = $db->query('SELECT COUNT(*) as count FROM users WHERE role = "admin"');
    $row = $result->fetchArray();
    if ($row['count'] == 0) {
        $hashedPassword = password_hash("P@\$\$w0rd", PASSWORD_DEFAULT);
        $db->exec('INSERT INTO users (email, password, role, can_login, can_access_transaction, can_access_bucket, can_access_report) 
        VALUES ("aa@aa.aa", "' . $hashedPassword . '", "admin", 1, 1, 1, 1)');
    }

   // Create transactions table
$db->exec('CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    date TEXT NOT NULL,
    description TEXT NOT NULL,
    amount REAL NOT NULL
)');

    // Create the buckets table and make category and description unique
    $db->exec('CREATE TABLE IF NOT EXISTS buckets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category TEXT NOT NULL ,
        description TEXT NOT NULL UNIQUE,
        UNIQUE(description) ON CONFLICT REPLACE


        
    )');
}

function insertSampleUserData($db) {
    $result = $db->query('SELECT COUNT(*) as count FROM users WHERE role = "user"');
    $row = $result->fetchArray();
    if ($row['count'] == 0) {
        $password = password_hash('123123', PASSWORD_DEFAULT);
        $SQL_insert_data = 
        "INSERT INTO users (email, password, role, can_login, can_access_transaction, can_access_bucket, can_access_report) 
            VALUES 
            ('bb@bb.bb', '$password', 'user', 1, 1, 1, 1),
            ('cc@cc.cc', '$password', 'user', 0, 0, 0, 0),
            ('dd@dd.dd', '$password', 'user', 1, 0, 0, 1)";
        $db->exec($SQL_insert_data);
    }
    }
?>
