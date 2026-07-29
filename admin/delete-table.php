<?php
//Start session
session_start();

//checking connection and connecting to a database
require_once('connection/config.php');

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

// check if Delete is set in POST
if (isset($_POST['Delete']) && isset($_POST['table'])) {
  // get id value of table and Sanitize the POST value
  $table_id = clean($link, $_POST['table']);

  // delete the entry using prepared statements
  $stmt = mysqli_prepare($link, "DELETE FROM tables WHERE table_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $table_id);
  $result = mysqli_stmt_execute($stmt);

  if ($result) {
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    // redirect back to options
    header("Location: options.php");
    exit();
  } else {
    $error = mysqli_error($link);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    die("There was a problem while deleting the table ... \n" . $error);
  }
} else {
  mysqli_close($link);
  // if id isn't set, redirect back to options
  header("Location: options.php");
  exit();
}
