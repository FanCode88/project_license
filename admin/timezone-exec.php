<?php
//Start session
session_start();
require_once('auth.php');

//Include database connection details
require_once('connection/config.php');

//Connect to mysql server using modern MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

//Set charset to utf8mb4 for proper character encoding
mysqli_set_charset($link, "utf8mb4");

//Function to sanitize values received from the form. Prevents SQL injection
function clean($conn, $str)
{
  $str = trim($str);
  return mysqli_real_escape_string($conn, $str);
}

//Sanitize the POST values
$name = isset($_POST['name']) ? clean($link, $_POST['name']) : '';

//define a default value for flag
$flag_0 = 0;

//Create INSERT query using prepared statements
$stmt = mysqli_prepare($link, "INSERT INTO timezones (timezone_reference, flag) VALUES (?, ?)");
if ($stmt) {
  mysqli_stmt_bind_param($stmt, "si", $name, $flag_0);
  $result = mysqli_stmt_execute($stmt);

  //Check whether the query was successful or not
  if ($result) {
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    header("location: options.php");
    exit();
  } else {
    $error = mysqli_error($link);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    die("Query failed " . $error);
  }
} else {
  $error = mysqli_error($link);
  mysqli_close($link);
  die("Query preparation failed " . $error);
}
