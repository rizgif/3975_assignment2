<?php
include("inc_header.php");
$db = new SQLite3('mydatabase.db');
$allowed_categories = ['Entertainment', 'Donations', 'Communication', 'Groceries', 'Car Insurance', 'Other', 'Gas Heating', 'Utilities'];

if (isset($_POST['update'])) {
    $id = SQLite3::escapeString($_POST['id']);
    $category = $_POST['category']; // We will ensure this is a valid category from the dropdown
    $description = SQLite3::escapeString($_POST['description']); // Changed 'name' to 'description'
  
    // Ensure that the provided category is in the list of allowed categories
    if (!in_array($category, $allowed_categories)) {
        header('Location: buckets_update.php?id=' . urlencode($id) . '&error=Invalid category selected.');
        exit;
    }

    $stmt = $db->prepare('UPDATE buckets SET category = :category, description = :description WHERE id = :id');
    $stmt->bindValue(':category', $category, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
  
    if ($stmt->execute()) {
        header('Location: buckets.php?message=Bucket updated successfully');
        exit;
    } else {
        $error = $db->lastErrorMsg();
        header('Location: buckets_update.php?id=' . urlencode($id) . '&error=' . urlencode($error));
        exit;
    }
} elseif (isset($_GET['id'])) {
    $id = SQLite3::escapeString($_GET['id']);
    $result = $db->querySingle("SELECT * FROM buckets WHERE id = '$id'", true);
} else {
    header('Location: buckets.php');
    exit;
}

?>

<h2>Update Bucket</h2>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<form method="post" action="buckets_update.php">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($result['id']); ?>">
    <div class="form-group">
        <label for="category">Category:</label>
        <select class="form-control" id="category" name="category" required>
            <?php foreach ($allowed_categories as $allowed_category): ?>
                <option value="<?php echo htmlspecialchars($allowed_category); ?>"<?php if ($result['category'] === $allowed_category) echo ' selected'; ?>>
                    <?php echo htmlspecialchars($allowed_category); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($result['description']); ?>" required>
    </div>

    <button type="submit" class="btn btn-warning" name="update">Update</button>
    <a href="/buckets.php" class="btn btn-small btn-primary">&lt;&lt; BACK</a>
</form>

<?php include("inc_footer.php"); ?>
