<?php
session_start();
include 'database_connection.php';

// Fetch the role of the user from the database to set the access to modify buckets
if (isset($_SESSION['email'])) {
    $stmt = $db->prepare('SELECT role, can_access_bucket FROM users WHERE email = :email');
    $stmt->bindValue(':email', $_SESSION['email'], SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);
    $_SESSION['role'] = $user['role'];
    $can_access_bucket = $row['can_access_bucket'];
}

// Check if the user can access the buckets page
if ($can_access_bucket != 1) {
    header('Location: /access_error.php');
    exit;
}

// Function to parse and insert CSV data into the buckets table
function parseAndInsertCSV($csvFile, $db) {
    // Check if the database connection is successful
    if (!$db) {
        die("Database connection failed.");
    }

    // Keyword-category mapping
    $keywordsAndCategories = array(
        'ST JAMES RESTAURAT' => 'Entertainment',
        'RED CROSS' => 'Donations',
        'GATEWAY' => 'Communication',
        'SAFEWAY' => 'Groceries',
        'Subway' => 'Entertainment',
        'PUR & SIMPLE RESTAUR' => 'Entertainment',
        'REAL CDN SUPERS' => 'Groceries',
        'ICBC' => 'Car Insurance',
        'FORTISBC' => 'Gas Heating',
        'BMO' => 'Other',
        'WALMART' => 'Groceries',
        'COSTCO' => 'Groceries',
        'MCDONALDS' => 'Entertainment',
        'WHITE SPOT RESTAURAN' => 'Entertainment',
        'SHAW CABLE' => 'Utilities',
        'CANADIAN TIRE' => 'Other',
        'World Vision' => 'Donations',
        '7-ELEVEN' => 'Other',
        'TIM HORTONS' => 'Entertainment',
        'ROGERS MOBILE' => 'Communication',
        'O.D.P. FEE' => 'Other',
        'MONTHLY ACCOUNT FEE' => 'Other'
        // Add more keywords and corresponding categories as needed
    );

    // Open the CSV file
    $file = fopen($csvFile, 'r');
    if (!$file) {
        die("Error opening file $csvFile");
    }

    // Prepare the SQL statement to insert data into the buckets table
    $stmt = $db->prepare('INSERT INTO buckets (category, description) VALUES (:category, :description)');

    // Loop through each line in the CSV file
    while (($line = fgetcsv($file)) !== false) {
        // Parse CSV data
        $description = $line[1];
        $category = getCategoryForDescription($description, $keywordsAndCategories);

        // Bind parameters
        $stmt->bindParam(':category', $category, SQLITE3_TEXT);
        $stmt->bindParam(':description', $description, SQLITE3_TEXT);

        // Execute the prepared statement
        if (!$stmt->execute()) {
            die("Failed to execute SQL statement.");
        }
    }

    // Close the file and database connection
    fclose($file);
    include 'inc_header.php';
}



// Function to determine category for a description based on keywords
function getCategoryForDescription($description, $keywordsAndCategories) {
    foreach ($keywordsAndCategories as $keyword => $category) {
        if (stripos($description, $keyword) !== false) {
            return $category;
        }
    }
    // If no category found, return 'Other' or handle it as needed
    return 'Other';
}

echo '<div class="container">';
// Let only admin to add new bucket
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    echo '<a href="/buckets_add.php" class="btn btn-info">Add New Bucket</a> ';
}
echo '<a href="/" class="btn btn-primary">&lt;&lt; BACK</a>';
echo '</div>';
?>

<?php
// If bucket is empty, insert sample bucket data
$res = $db->query('SELECT COUNT(*) FROM buckets');
$row = $res->fetchArray();
$res->finalize();
if ($row[0] == 0) {
    parseAndInsertCSV('2023 02.csv', $db);
}

// Fetch all descriptions from the transactions table
$transactionRes = $db->query('SELECT DISTINCT description FROM transactions');
$transactionDescriptions = [];
while ($row = $transactionRes->fetchArray()) {
    $transactionDescriptions[] = $row['description'];
}

// Fetch all descriptions from the buckets table
$bucketRes = $db->query('SELECT DISTINCT description FROM buckets');
$bucketDescriptions = [];
while ($row = $bucketRes->fetchArray()) {
    $bucketDescriptions[] = trim($row['description']);
}



// Find descriptions that exist in transactions but not in buckets
$uncategorizedDescriptions = [];
foreach ($transactionDescriptions as $transaction) {
    $found = false;
    foreach ($bucketDescriptions as $bucket) {
        if (stripos($transaction, $bucket) !== false) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $uncategorizedDescriptions[] = $transaction;
    }
}
?>

<!-- Display uncategorized transaction list -->
<div class="container">
    
    <h3>List of uncategorized transactions</h3>
    <?php
    if (count($uncategorizedDescriptions) == 0) {
        echo '<p>No uncategorized transactions found.</p>';
    } else {
        echo '<h5>The following transactions need to be categorized:</h5>';
    } ?>
    <?php foreach ($uncategorizedDescriptions as $transaction) : ?>
        <p><?php echo $transaction ?></p>
    <?php endforeach; ?>
</div>

<?php
// Display buckets list
echo '<div class="container">';
echo '<h2 class="mt-3">List of Buckets</h2>';
echo '<table class="table table-striped">';
echo '<thead>';
echo '<tr>';
echo '<th scope="col">ID</th>';
echo '<th scope="col">Category</th>';
echo '<th scope="col">Description</th>';
echo '<th scope="col">Actions</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Adjusted query to ensure only unique category and description pairs are shown
$res = $db->query('SELECT id, category, description FROM buckets GROUP BY category, description ORDER BY id');

while ($row = $res->fetchArray()) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
    echo '<td>' . htmlspecialchars($row['category']) . '</td>';
    echo '<td>' . htmlspecialchars($row['description']) . '</td>';
    echo '<td>';

    // Check if the user's role is 'admin'
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        // Display the buttons only to admin
        echo '<a href="buckets_update.php?id=' . urlencode($row['id']) . '" class="btn btn-warning">Update</a> ';
        echo '<a href="buckets_delete.php?id=' . urlencode($row['id']) . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this bucket?\')">Delete</a>';
    }
    echo '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
echo '</div>';

$db->close();

include 'inc_footer.php';
?>
