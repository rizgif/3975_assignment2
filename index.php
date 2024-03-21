<?php
session_start();


include 'inc_header.php';
include 'database_connection.php';


$db = getDatabaseConnection();
createTables($db);   // Ensure database connection is available
insertSampleUserData($db);
?>

<style>
  .btn {
    margin-bottom: 10px;
  }
</style>

<div class="container text-center">
  <?php
  if (!isset($_SESSION['email'])) {
    // Before login
  ?>
    <button class="btn btn-primary" onclick="window.location.href = 'login.php'">Login</button>
    <button class="btn btn-success" onclick="window.location.href = 'register.php'">Register</button>
  <?php
  } else {
    // After login
    $result = $db->query('SELECT role, can_access_transaction, can_access_bucket, can_access_report FROM users WHERE email = "' . $_SESSION['email'] . '"');
    $row = $result->fetchArray();
    $role = $row['role'];
    $can_access_transaction = $row['can_access_transaction'];
    $can_access_bucket = $row['can_access_bucket'];
    $can_access_report = $row['can_access_report'];
  ?>

    <div class="text-center">
      <h4>Logged in as <?php echo $_SESSION['email']; ?></h4>

      <!-- logout button -->
      <form action="logout.php" method="post" style="display: inline-block;">
        <button type="submit" class="btn btn-danger">Logout</button>
      </form>

      <!-- Manage Users button to admin -->
      <?php if ($role == 'admin') { ?>
        <div>
          <button class="btn btn-info" onclick="window.location.href = 'admin.php'">Manage Users</button>
        </div>
      <?php }

      // Go To Transactions button to authorized users
      if ($can_access_transaction) { ?>
        <div>
          <button type="button" class="btn btn-primary" onclick="location.href='transactions.php'">Go To Transactions</button>
        </div>

        <!-- Add the Go To Buckets Data button -->
      <?php }
      if ($can_access_bucket) { ?>
        <div>
          <button type="button" class="btn btn-primary" onclick="location.href='buckets.php'">Go To Buckets Data</button>
        </div>
      <?php } ?>

      <!-- Add the Generate Report button to authorized users -->
      <?php if ($can_access_report) { ?>
        <div>
          <button type="button" class="btn btn-success mt-3" onclick="generateReport()">Reports</button>
        </div>
      <?php } ?>

      <!-- Modified File Upload Form for Admins or Approved Users to accept multiple files -->
      <div class="upload-section mt-4">
        <h5>Upload CSV Files:</h5>
        <form action="upload.php" method="post" enctype="multipart/form-data">
          Select CSV files to upload and hit Upload Files button:<br>
          <center>
            <input type="file" name="filesToUpload[]" id="filesToUpload" accept=".csv" multiple>
          </center><br>
          <input type="submit" value="Upload Files" name="submit" class="btn btn-secondary">
        </form>
      </div>


    </div>
  <?php } ?>
</div>

<?php 
$db->close();
include 'inc_footer.php'; 
?>


<!-- footer -->
<footer class="footer bg-light mt-4">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <hr>
        <div class="text-right">
          <p class="mb-0">Riz Nur Saidy</p>
          <p class="mb-0">Diane Choi</p>
        </div>
      </div>
    </div>
  </div>
</footer>

<?php include 'inc_footer.php'; ?>

<!-- JavaScript function to handle report generation -->
<script>
  function generateReport() {
    // Redirect to the report generation page
    window.location.href = 'generate_report.php';
  }
</script>