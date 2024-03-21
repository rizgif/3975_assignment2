<?php
$db = new SQLite3('mydatabase.db');
// Include config file
include_once "database_setup.php";

// Define variables and initialize with empty values
$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate email
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter an email.";
  } else {
    $email = trim($_POST["email"]);
    $SQL_check_email = "SELECT COUNT(*) FROM users WHERE email = :email";
    $stmt = $db->prepare($SQL_check_email);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $count = $result->fetchArray()[0];
    if ($count > 0) {
      $email_err = "This email is already taken.";
    }
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST["password"])) < 6) {
    $password_err = "Password must have at least 6 characters.";
  } else {
    $password = trim($_POST["password"]);
  }

  // Validate confirm password
  if (empty(trim($_POST["confirm_password"]))) {
    $confirm_password_err = "Please confirm password.";
  } else {
    $confirm_password = trim($_POST["confirm_password"]);
    if (empty($password_err) && ($password != $confirm_password)) {
      $confirm_password_err = "Password did not match.";
    }
  }

  // Check input errors before inserting in database
  if (empty($email_err) && empty($password_err) && empty($confirm_password_err)) {

    // Prepare an insert statement
    $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";

    if ($stmt = $db->prepare($sql)) {
      // Bind variables to the prepared statement as parameters
      $stmt->bindParam(":email", $param_email, SQLITE3_TEXT);
      $stmt->bindParam(":password", $param_password, SQLITE3_TEXT);

      // Set parameters
      $param_email = $email;
      $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Redirect to login page
        header("location: /");
        exit();
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      $stmt->close();
    }
  }
}
include 'inc_header.php';
?>
  <style>
    body {
      font: 14px sans-serif;
    }

    .wrapper {
      width: 360px;
      padding: 20px;
    }
  </style>
  <div class="wrapper">
    <h2>Sign Up</h2>
    <p>Please fill this form to create an account.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
        <span class="help-block"><?php echo $email_err; ?></span>
      </div>
      <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label>Password</label>
        <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
        <span class="help-block"><?php echo $password_err; ?></span>
      </div>
      <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
        <span class="help-block"><?php echo $confirm_password_err; ?></span>
      </div>
      <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
      </div>
      <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
  </div>
<?php include 'inc_footer.php'; ?>