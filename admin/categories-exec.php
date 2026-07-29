<?php
//Start session
session_start();

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
function clean($link, $str)
{
  $str = trim($str);
  return mysqli_real_escape_string($link, $str);
}

//Sanitize the POST values
$name = clean($link, $_POST['name']);

//Create INSERT query using prepared statements for maximum security
$stmt = mysqli_prepare($link, "INSERT INTO categories(category_name) VALUES(?)");
mysqli_stmt_bind_param($stmt, "s", $name);
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
