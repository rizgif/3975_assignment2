<?php
// include header
include("inc_header.php");
$db = new SQLite3('mydatabase.db');

if (isset($_GET['id'])) {
  $id = SQLite3::escapeString($_GET['id']); // Retrieve the ID from GET parameter

  $sql = "DELETE FROM buckets WHERE id = '$id'"; // Update the table name to 'buckets'

  if ($db->exec($sql)) {
    // Redirect on successful deletion
    header('Location: buckets.php?message=Bucket deleted successfully'); // Update the redirection URL to 'buckets.php'
    exit;
  } else {
    $error = $db->lastErrorMsg();
    // Handle any errors and redirect back with an error message
    header('Location: buckets.php?error=' . urlencode($error)); // Update the redirection URL to 'buckets.php'
    exit;
  }
} else {
  // Redirect if the id is not provided in the URL
  header('Location: buckets.php'); // Update the redirection URL to 'buckets.php'
  exit;
}
include("inc_footer.php");
?>
