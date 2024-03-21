<?php
$db = new SQLite3('mydatabase.db');

if (isset($_POST['create'])) {
  $date = SQLite3::escapeString($_POST['date']);
  $description = SQLite3::escapeString($_POST['description']);
  $amount = SQLite3::escapeString($_POST['amount']);
  // $category = SQLite3::escapeString($_POST['category']); 

  $sql = "INSERT INTO transactions (date, description, amount) VALUES ('$date', '$description', '$amount')";

  if ($db->exec($sql)) {
    header('Location: transactions.php');
    exit;
  } else {
    $error = $db->lastErrorMsg();
    header('Location: create.php?error=' . urlencode($error));
    exit;
  }
} else {
  header('Location: transactions_add.php');
  exit;
}
