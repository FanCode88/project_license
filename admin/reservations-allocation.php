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

// Sanitize and validate POST values using prepared statements / modern approach
$ReservationID = $_POST['reservationid'] ?? '';
$StaffID = $_POST['staffid'] ?? '';

// Basic validation to ensure fields are not empty or set to default "select"
if ($ReservationID === 'select' || $StaffID === 'select' || empty($ReservationID) || empty($StaffID)) {
  die("Error: Invalid reservation or staff selection.");
}

// Define a default value for flag
$flag_1 = 1;

// Update the entry using prepared statements to prevent SQL injection
$query = "UPDATE reservations_details SET StaffID = ?, flag = ? WHERE ReservationID = ?";
$stmt = mysqli_prepare($link, $query);

if ($stmt) {
  mysqli_stmt_bind_param($stmt, "iii", $StaffID, $flag_1, $ReservationID);
  $result = mysqli_stmt_execute($stmt);

  if ($result) {
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    // Redirect back to the allocation page
    header("Location: allocation.php");
    exit();
  } else {
    $error = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    die("Reservation allocation failed ... \n" . $error);
  }
} else {
  $error = mysqli_error($link);
  mysqli_close($link);
  die("Query preparation failed ... \n" . $error);
}
?>
