<?php
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Connect to SQLite database
  $db = new SQLite3('mydatabase.db');

  // Get email and password from form
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Query the database for the user
  $stmt = $db->prepare('SELECT * FROM users WHERE email=:email');
  $stmt->bindValue(':email', $email);
  $result = $stmt->execute();

  // Check if the user exists and the password is correct
  if ($result && $row = $result->fetchArray()) {
    if (password_verify($password, $row['password'])) {
      if ($row['can_login'] == 0) {
        // User not approved
        $login_err = "Your account is not approved yet";
      } else {
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $row['id'];
        $_SESSION["email"] = $email;

        header("location: index.php");
      }
    } else {
      $login_err = "The password is invalid.";
    }
  } else {
    $login_err = "Please check the email again.";
  }
  $db->close();
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
    <h2>Login</h2>
    <p></p>

    <?php if (!empty($login_err)) : ?>
      <div class="alert alert-danger"><?php echo $login_err; ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control">
      </div>
      <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Login">
      </div>
      <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
    </form>
  </div>
<?php include 'inc_footer.php'; ?>