<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include auth.php care pornește deja sesiunea și verifică autentificarea
require_once('auth.php');

$id = isset($_SESSION['SESS_MEMBER_ID']) ? $_SESSION['SESS_MEMBER_ID'] : null;

// Include database connection details
require_once('connection/config.php');

// Connect to mysql server using modern MySQLi
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

// Sanitize the POST values
$partyhall_id = 0;
$table_id = 0;
$partyhall_flag = 0;
$table_flag = 0;

if (isset($_POST['table'])) {
  $table_id = clean($link, $_POST['table']);
  $table_flag = 1;
} else if (isset($_POST['partyhall'])) {
  $partyhall_id = clean($link, $_POST['partyhall']);
  $partyhall_flag = 1;
}

$date = isset($_POST['date']) ? clean($link, $_POST['date']) : '';
$time = isset($_POST['time']) ? clean($link, $_POST['time']) : '';

// Valori implicite pentru StaffID și flag
$staff_id = 0;
$flag = 0;

// check if the id is set at the link
if (isset($_GET['id'])) {
  // get user id and sanitize
  $id = clean($link, $_GET['id']);

  // Create INSERT query including StaffID and flag to satisfy table requirements
  $stmt = mysqli_prepare($link, "INSERT INTO reservations_details (member_id, table_id, partyhall_id, Reserve_Date, Reserve_Time, table_flag, partyhall_flag, StaffID, flag) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  if ($stmt) {
    // Tipurile parametrilor: i = integer, s = string (9 parametri totali: iiissiiii)
    mysqli_stmt_bind_param($stmt, "iiissiiii", $id, $table_id, $partyhall_id, $date, $time, $table_flag, $partyhall_flag, $staff_id, $flag);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
      mysqli_stmt_close($stmt);
      mysqli_close($link);
      // redirect to the reserve success page
      header("location: reserve-success.php");
      exit();
    } else {
      $error = mysqli_error($link);
      mysqli_stmt_close($stmt);
      mysqli_close($link);
      die("Reservation failed! Please try again after a few minutes ... \n" . $error);
    }
  } else {
    $error = mysqli_error($link);
    mysqli_close($link);
    die("Query preparation failed ... \n" . $error);
  }
} else {
  mysqli_close($link);
  die("Reservation failed! Please try again after a few minutes.");
}
