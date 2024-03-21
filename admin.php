<?php
include 'inc_header.php';
$result = $db->query('SELECT * FROM users WHERE role = "user"');
?>

<div class="container">
  <h2 class="text-center">Grant users access</h2>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Email</th>
        <th>Role</th>
        <th>Login approval</th>
        <th>Transactions</th>
        <th>Buckets</th>
        <th>Report</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Loop through the users
      while ($row = $result->fetchArray()) {
      ?>
        <tr>
          <td><?php echo $row['email']; ?></td>
          <td><?php echo $row['role']; ?></td>
          <td>
            <!-- Login approval -->
            <form action="access_update.php" method="post">
              <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
              <input type="hidden" name="access_type" value="can_login">
              <input type="checkbox" name="access_value" value="1" <?php echo ($row['can_login'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit()">
            </form>
          </td>
          <td>
            <!-- Transactions access control -->
            <form action="access_update.php" method="post">
              <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
              <input type="hidden" name="access_type" value="can_access_transaction">
              <input type="checkbox" name="access_value" value="1" <?php echo ($row['can_access_transaction'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit()">
            </form>
          </td>
          <!-- Buckets access control -->
          <td>
            <form action="access_update.php" method="post">
              <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
              <input type="hidden" name="access_type" value="can_access_bucket">
              <input type="checkbox" name="access_value" value="1" <?php echo ($row['can_access_bucket'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit()">
            </form>
          </td>
          <!-- Report access control -->
          <td>
            <form action="access_update.php" method="post">
              <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
              <input type="hidden" name="access_type" value="can_access_report">
              <input type="checkbox" name="access_value" value="1" <?php echo ($row['can_access_report'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit()">
            </form>
          </td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>

  <button class="btn btn-primary" onclick="window.location.href = 'index.php'">Back</button>
  <?php include 'inc_footer.php'; ?>