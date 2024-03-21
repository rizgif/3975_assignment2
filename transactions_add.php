<?php 
  session_start();
  include("inc_header.php");

  $error_message = isset($_GET['error']) ? $_GET['error'] : '';
  $stmt = $db->prepare('SELECT can_access_transaction FROM users WHERE email = :email');
  $stmt->bindValue(':email', $_SESSION['email'], SQLITE3_TEXT);
  $result = $stmt->execute();
  $row = $result->fetchArray();
  $can_access_transaction = $row['can_access_transaction'];

  // Check if the user can access  the transaction page
  if ($can_access_transaction != 1) {
    header('Location: /access_error.php');
    exit;
  }
?>

<h2>Add New Transaction</h2>

<div class="row">
  <div class="col-md-4">
    <?php if (!empty($error_message)) : ?>
      <div class="alert alert-danger">
        <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>
    
    <!-- Form to add a new transaction -->
    <form action="transactions_add_process.php" method="post">
      <div class="form-group">
        <label for="date" class="control-label">Date</label>
        <input type="date" class="form-control" name="date" id="date" required />
      </div>

      <div class="form-group">
        <label for="description" class="control-label">Description</label>
        <input type="text" class="form-control" name="description" id="description" required />
      </div>

      <div class="form-group">
        <label for="amount" class="control-label">Amount</label>
        <input type="number" class="form-control" name="amount" id="amount" step="0.01" required />
      </div>

      <div class="form-group">
        <a href="/transactions.php" class="btn btn-small btn-primary">&lt;&lt; BACK</a>
        &nbsp;&nbsp;&nbsp;
        <input type="submit" value="Create" name="create" class="btn btn-success" />
      </div>
    </form>
  </div>
</div>

<?php include("inc_footer.php"); ?>