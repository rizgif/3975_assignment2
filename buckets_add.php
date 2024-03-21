<?php 
session_start();
include("inc_header.php");
$error_message = isset($_GET['error']) ? $_GET['error'] : '';
$allowed_categories = ['Entertainment', 'Donations', 'Communication', 'Groceries', 'Car Insurance', 'Other', 'Gas Heating', 'Utilities'];

$stmt = $db->prepare('SELECT can_access_bucket FROM users WHERE email = :email');
$stmt->bindValue(':email', $_SESSION['email'], SQLITE3_TEXT);
$result = $stmt->execute();
$row = $result->fetchArray();
$can_access_bucket = $row['can_access_bucket'];

// Check if the user can access the bucket page
if ($can_access_bucket != 1) {
  header('Location: /access_error.php');
  exit;
}

?>

<h2>Add New Bucket</h2>

<div class="row">
  <div class="col-md-4">
    <?php if (!empty($error_message)) : ?>
      <div class="alert alert-danger">
        <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>
    <form action="buckets_add_process.php" method="post">

      <div class="form-group">
        <label for="category" class="control-label">Category</label>
        <select class="form-control" name="category" id="category" required>
          <?php foreach ($allowed_categories as $category): ?>
            <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="description" class="control-label">Description</label>
        <input type="text" class="form-control" name="description" id="description" required />
      </div>

      <div class="form-group">
        <a href="/buckets.php" class="btn btn-small btn-primary">&lt;&lt; Back</a>
        &nbsp;&nbsp;&nbsp;
        <input type="submit" value="Create" name="create" class="btn btn-success" />
      </div>
    </form>
  </div>
</div>

<?php include("inc_footer.php"); ?>
