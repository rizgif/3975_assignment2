<?php 
  session_start();
  include 'inc_header.php';
  include 'database_connection.php';

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

  // Check if the transactions table is empty
  $sql = "SELECT COUNT(*) FROM transactions";
  $result = $db->query($sql);
  $row = $result->fetchArray();
  if ($row[0] == 0) {
  // Open the CSV file
  if (($handle = fopen("2023 02.csv", "r")) !== FALSE) {
    $data = fgetcsv($handle, 1000, ",");
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $date = DateTime::createFromFormat('m/d/Y', $data[0]);
      $formattedDate = $date ? $date->format('Y-m-d') : ''; // adjust date format to yy-mm-dd

      $num = count($data);

      $formattedDate = SQLite3::escapeString($formattedDate);
      $description = SQLite3::escapeString($data[1]);
      $amount = SQLite3::escapeString($data[2]);
      $category = '';

      if (!empty($amount)) {
        $SQLinsert = "INSERT INTO transactions (date, description, amount)";
        $SQLinsert .= " VALUES ";
        $SQLinsert .= " ('$formattedDate', '$description', '$amount')";

        $db->exec($SQLinsert);
        $changes = $db->changes();
      }
    }
    fclose($handle);
  }
  }

?>

<!-- display transactions list -->
<div class="container">
  <h2 class="mt-3">List of transactions</h2>
  <a href="/transactions_add.php" class="btn btn-info ">Add New Transaction</a>
  <a href="/" class="btn btn-primary ">&lt;&lt; BACK</a>
  <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">ID</th>
        <th scope="col">Date</th>
        <th scope="col">Description</th>
        <th scope="col">Amount</th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $res = $db->query('SELECT * FROM transactions');

      while ($row = $res->fetchArray()) {
        echo "<tr>\n";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['date']}</td>";
        echo "<td>{$row['description']}</td>";
        echo "<td>\${$row['amount']}</td>";
        echo "<td>
        <a href='transactions_update.php?id={$row['id']}' class='btn btn-warning'>Update</a>
        <a href='transactions_delete.php?id={$row['id']}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this transaction?\")'>Delete</a>
      </td>";
        echo "</tr>\n";
      }
      $db->close();
      ?>
    </tbody>
  </table>
</div>

<?php include 'inc_footer.php'; ?>