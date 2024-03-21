<?php
$db = new SQLite3('mydatabase.db');

if (isset($_POST['create'])) {
  $category = $_POST['category']; // No need to escape this because it comes from a controlled set
  $description = SQLite3::escapeString($_POST['description']); // Description should still be escaped or parameterized

  // Check if the category is allowed
  if (!in_array($category, ['Entertainment', 'Donations', 'Communication', 'Groceries', 'Car Insurance', 'Other', 'Gas Heating', 'Utilities'])) {
    header('Location: buckets_add.php?error=' . urlencode('Invalid category selected.'));
    exit;
  }

  // Check if the description already exists in the buckets table
  $checkStmt = $db->prepare('SELECT COUNT(*) FROM buckets WHERE description = :description');
  $checkStmt->bindValue(':description', $description, SQLITE3_TEXT);
  $result = $checkStmt->execute();
  $row = $result->fetchArray();
  $count = $row[0];

  if ($count > 0) {
    // If description already exists, update the existing row
    $updateStmt = $db->prepare('UPDATE buckets SET category = :category WHERE description = :description');
    $updateStmt->bindValue(':category', $category, SQLITE3_TEXT);
    $updateStmt->bindValue(':description', $description, SQLITE3_TEXT);

    if ($updateStmt->execute()) {
      header('Location: buckets.php');
      exit;
    } else {
      $error = $db->lastErrorMsg();
      header('Location: buckets_add.php?error=' . urlencode($error));
      exit;
    }
  } else {
    // If description doesn't exist, insert a new row
    $insertStmt = $db->prepare('INSERT INTO buckets (category, description) VALUES (:category, :description)');
    $insertStmt->bindValue(':category', $category, SQLITE3_TEXT);
    $insertStmt->bindValue(':description', $description, SQLITE3_TEXT);

    if ($insertStmt->execute()) {
      header('Location: buckets.php');
      exit;
    } else {
      $error = $db->lastErrorMsg();
      header('Location: buckets_add.php?error=' . urlencode($error));
      exit;
    }
  }
} else {
  header('Location: buckets_add.php');
  exit;
}
?>
