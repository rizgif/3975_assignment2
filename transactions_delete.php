<?php
// include header
include("inc_header.php");
$db = new SQLite3('mydatabase.db');

if (isset($_GET['id'])) {
  $id = SQLite3::escapeString($_GET['id']); // Retrieve the ID from GET parameter

  $sql = "DELETE FROM transactions WHERE id = '$id'";

  if ($db->exec($sql)) {
    // Redirect on successful deletion
    header('Location: transactions.php?message=Transaction deleted successfully');
    exit;
  } else {
    $error = $db->lastErrorMsg();
    // Handle any errors and redirect back with an error message
    header('Location: transactions.php?error=' . urlencode($error));
    exit;
  }
} else {
  // Redirect if the id is not provided in the URL
  header('Location:transactions.php');
  exit;
}
include("inc_footer.php");
?>