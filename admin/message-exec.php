<?php
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

//retrive a timezone from the timezones table using prepared statements
//define a default value for flag_1
$flag_1 = 1;
$stmt_tz = mysqli_prepare($link, "SELECT timezone_reference FROM timezones WHERE flag = ?");
mysqli_stmt_bind_param($stmt_tz, "i", $flag_1);
mysqli_stmt_execute($stmt_tz);
$timezones = mysqli_stmt_get_result($stmt_tz);

$active_reference = "UTC"; // default fallback
if ($timezones && $row = mysqli_fetch_assoc($timezones)) {
  $active_reference = $row['timezone_reference']; //gets active timezone
}
mysqli_stmt_close($stmt_tz);

date_default_timezone_set($active_reference); //sets the default timezone for use

$current_date = date("Y-m-d"); //gets the current date
$current_time = date("H:i:s"); //gets the current time

//Sanitize the POST values
$new_subject = isset($_POST['subject']) ? clean($link, $_POST['subject']) : '';
$new_message = isset($_POST['txtmessage']) ? clean($link, $_POST['txtmessage']) : '';

$from = "administrator"; //sets default to the administrator

// update the entry using prepared statements
$stmt = mysqli_prepare($link, "INSERT INTO messages(message_from, message_date, message_time, message_subject, message_text) VALUES(?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sssss", $from, $current_date, $current_time, $new_subject, $new_message);
$result = mysqli_stmt_execute($stmt);

if ($result) {
  mysqli_stmt_close($stmt);
  mysqli_close($link);
  // redirect back to the messages page
  header("Location: messages.php");
  exit();
} else {
  $error = mysqli_error($link);
  mysqli_stmt_close($stmt);
  mysqli_close($link);
  // if not sent, give an error
  die("Message sending failed ..." . $error);
}
