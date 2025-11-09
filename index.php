<?php
session_start();
include 'db.php'; // make sure this file connects to book_review_app

// Registration logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['regName'])) {
    $name = mysqli_real_escape_string($conn, $_POST['regName']);
    $email = mysqli_real_escape_string($conn, $_POST['regEmail']);
    $password = password_hash($_POST['regPassword'], PASSWORD_DEFAULT);

    $checkEmail = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($checkEmail) > 0) {
        echo "<script>alert('Email already exists');</script>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (username, email, password) VALUES ('$name', '$email', '$password')");
        if ($insert) {
            echo "<script>alert('Registration successful! Please sign in.');</script>";
        } else {
            echo "<script>alert('Registration failed.');</script>";
        }
    }
}

// Login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginEmail'])) {
    $email = mysqli_real_escape_string($conn, $_POST['loginEmail']);
    $password = $_POST['loginPassword'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($query) == 1) {
        $user = mysqli_fetch_assoc($query);
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            header("Location: home.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('Email not registered');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book Login/Register</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="container" id="container">
    <!-- Sign Up -->
    <div class="form-container sign-up-container">
      <form method="POST">
        <h1>Create Account</h1>
        <input type="text" name="regName" placeholder="Name" required />
        <input type="email" name="regEmail" placeholder="Email" required />
        <input type="password" name="regPassword" placeholder="Password" required />
        <button type="submit">Sign Up</button>
      </form>
    </div>

    <!-- Redundant form removed, handled above -->

    <!-- Sign In -->
    <div class="form-container sign-in-container">
      <form method="POST">
        <h1>Sign in</h1>
        <input type="email" name="loginEmail" placeholder="Email" required />
        <input type="password" name="loginPassword" placeholder="Password" required />
        <a href="#">Forgot your password?</a>
        <button type="submit">Sign In</button>

        <!-- Google Login Button -->
        <button type="button" onclick="window.location.href='google-login.php'">
          <img src="images/google.png" alt="Google Icon" width="20" />
          Sign in with Google
        </button>
      </form>
    </div>

    <!-- Overlay -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Welcome Back!</h1>
          <p>Login to track and review your favorite books</p>
          <button class="ghost" id="signIn">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Hello, Reader!</h1>
          <p>Sign up and start your reading journey!</p>
          <button class="ghost" id="signUp">Sign Up</button>
        </div>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
