<?php
include("inc_header.php");
$db = new SQLite3('mydatabase.db');

if (isset($_POST['update'])) {
  $id = SQLite3::escapeString($_POST['id']);
  $description = SQLite3::escapeString($_POST['description']);
  $amount = SQLite3::escapeString($_POST['amount']);

  $sql = "UPDATE transactions SET description = '$description', amount = '$amount' WHERE id = '$id'";

  if ($db->exec($sql)) {
    header('Location: transactions.php?message=Transaction updated successfully');
    exit;
  } else {
    $error = $db->lastErrorMsg();
    header('Location: transactions.php?error=' . urlencode($error));
    exit;
  }
} elseif (isset($_GET['id'])) {
  $id = SQLite3::escapeString($_GET['id']);
  $result = $db->querySingle("SELECT * FROM transactions WHERE id = '$id'", true);
} else {
  header('Location: transactions.php');
  exit;
}
?>


<h2>Update Transaction</h2>

<form method="post" action="transactions_update.php">
  <input type="hidden" name="id" value="<?php echo $result['id']; ?>">
  <div class="form-group">
    <label for="date">Date:</label>
    <input type="text" class="form-control" id="date" name="date" value="<?php echo $result['date']; ?>" readonly>
  </div>
  <div class="form-group">
    <label for="description">Description:</label>
    <input type="text" class="form-control" id="description" name="description" value="<?php echo $result['description']; ?>">
  </div>
  <div class="form-group">
    <label for="amount">Amount:</label>
    <input type="number" class="form-control" id="amount" name="amount" value="<?php echo $result['amount']; ?>" step="any">
  </div>

  <button type="submit" class="btn btn-warning" name="update">Update</button>
  <a href="/transactions.php" class="btn btn-small btn-primary">&lt;&lt; BACK</a>
</form>

<?php include("inc_footer.php"); ?>