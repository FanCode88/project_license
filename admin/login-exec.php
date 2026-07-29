<?php
//Start session
session_start();

//Include database connection details
require_once('connection/config.php');

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Connect to mysql server using modern MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

//Set charset to utf8mb4 for proper character encoding
mysqli_set_charset($link, "utf8mb4");

//Function to sanitize values received from the form. Prevents SQL injection
function clean($link, $str)
{
  $str = trim($str);
  return mysqli_real_escape_string($link, $str);
}

//Sanitize the POST values
$login = isset($_POST['login']) ? clean($link, $_POST['login']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

//Input Validations
if ($login == '') {
  $errmsg_arr[] = 'Username missing';
  $errflag = true;
}
if ($password == '') {
  $errmsg_arr[] = 'Password missing';
  $errflag = true;
}

//If there are input validations, redirect back to the login form
if ($errflag) {
  $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
  session_write_close();
  mysqli_close($link);
  header("location: login-form.php");
  exit();
}

//Create query using prepared statements to prevent SQL injection and support password hashing
$stmt = mysqli_prepare($link, "SELECT Admin_ID, Username, Password FROM pizza_admin WHERE Username = ?");
if ($stmt) {
  mysqli_stmt_bind_param($stmt, "s", $login);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($result && mysqli_num_rows($result) == 1) {
    $member = mysqli_fetch_assoc($result);

    // Check password (supports standard plain text comparison or modern password_verify if hashed)
    $is_password_valid = false;
    if (password_verify($password, $member['Password']) || $password === $member['Password']) {
      $is_password_valid = true;
    }

    if ($is_password_valid) {
      //Login Successful
      session_regenerate_id(true);
      $_SESSION['SESS_ADMIN_ID'] = $member['Admin_ID'];
      $_SESSION['SESS_ADMIN_NAME'] = $member['Username'];
      session_write_close();
      mysqli_stmt_close($stmt);
      mysqli_close($link);
      header("location: index.php");
      exit();
    } else {
      //Login failed (wrong password)
      mysqli_stmt_close($stmt);
      mysqli_close($link);
      header("location: login-failed.php");
      exit();
    }
  } else {
    //Login failed (user not found)
    if ($stmt) {
      mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
    header("location: login-failed.php");
    exit();
  }
} else {
  $error = mysqli_error($link);
  mysqli_close($link);
  die("Query failed: " . $error);
}
