<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('auth.php');

// checking connection and connecting to a database
require_once('connection/config.php');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

// Set charset to utf8mb4 for proper character encoding
mysqli_set_charset($link, "utf8mb4");

// Function to sanitize values received from the form. Prevents SQL injection
function clean($conn, $str)
{
  $str = trim($str);
  return mysqli_real_escape_string($conn, $str);
}

// define default value for flag
$flag_1 = 1;

// Sanitize the POST values
$OrderID = clean($link, $_POST['orderid']);
$StaffID = clean($link, $_POST['staffid']);

// update the entry using prepared statements or safe mysqli queries
$query = "UPDATE orders_details SET StaffID='$StaffID', flag='$flag_1' WHERE order_id='$OrderID'";
$result = mysqli_query($link, $query);

//check if query executed
if ($result) {
  // redirect back to the allocation page
  header("Location: allocation.php");
  exit();
} else {
  // Gives an error
  die("order allocation failed ..." . mysqli_error($link));
}
?>
