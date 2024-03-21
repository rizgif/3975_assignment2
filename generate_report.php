<?php
session_start();
include 'inc_header.php';
include 'database_connection.php';

$stmt = $db->prepare('SELECT can_access_report FROM users WHERE email = :email');
$stmt->bindValue(':email', $_SESSION['email'], SQLITE3_TEXT);
$result = $stmt->execute();
$row = $result->fetchArray();
$can_access_report = $row['can_access_report'];

// Check if the user can access the report page
if ($can_access_report != 1) {
  header('Location: /access_error.php');
  exit;
}

// Query the database to get distinct years from transactions
$years_res = $db->query("SELECT DISTINCT strftime('%Y', date) AS year FROM transactions ORDER BY year DESC");
$years = [];
while ($year_row = $years_res->fetchArray(SQLITE3_ASSOC)) {
    $years[] = $year_row['year'];
}

// Check if a specific year has been selected; if not, use the current year as default
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

echo '<div class="container">';
echo '<h2>Expenses Report</h2>';

// Dropdown form for selecting the year
echo '<form action="generate_report.php" method="post">';
echo '<div class="form-group">';
echo '<label for="yearSelect">Select Year: </label>';
echo '<select name="year" id="yearSelect" class="form-control">';
foreach ($years as $year) {
    echo "<option value='{$year}'" . ($selectedYear == $year ? " selected" : "") . ">{$year}</option>";
}
echo '</select><br>';
echo '<button type="submit" class="btn btn-primary">Submit</button>';
echo '</select>';
echo '</div>';
echo '</form>';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
echo '<table class="table">';
echo '<thead>';
echo '<tr>';
echo '<th>Category</th>';
echo '<th>Total Expense</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$db = getDatabaseConnection();
$query = "SELECT b.category, SUM(t.amount) AS total_expense 
          FROM transactions t
          INNER JOIN buckets b ON t.description LIKE '%' || b.description || '%'
          WHERE strftime('%Y', t.date) = :selectedYear
          GROUP BY b.category";
$stmt = $db->prepare($query);
$stmt->bindValue(':selectedYear', $selectedYear, SQLITE3_TEXT);

$res = $stmt->execute();
$data = [];

while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $data[] = $row;
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['category']) . '</td>';
    echo '<td>' . htmlspecialchars(number_format($row['total_expense'], 2)) . '</td>';
    echo '</tr>';
}

$db->close();

echo '</tbody>';
echo '</table>';

// Add Google Charts to create a pie chart
echo '<div id="piechart" style="width: 900px; height: 500px;"></div>';
}
// Change the "Back" button to redirect to index.php
echo '<a href="index.php" class="btn btn-primary">&lt;&lt; Back</a>';

echo '</div>';


include 'inc_footer.php';
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var data = google.visualization.arrayToDataTable([
        ['Category', 'Total Expense'],
        <?php
        foreach ($data as $row) {
            echo "['" . addslashes($row['category']) . "', " . $row['total_expense'] . "],";
        }
        ?>
    ]);

    var options = {
        title: 'Expenses by Category',
        pieHole: 0.4,
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
}
</script>
