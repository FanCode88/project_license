<?php
//Start session
session_start();

require_once('auth.php');

//Include database connection details
require_once('connection/config.php');

//Connect to mysql server using MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

//Function to sanitize values received from the form. Prevents SQL injection
function clean($link, $str)
{
  $str = @trim($str);
  if (get_magic_quotes_gpc()) {
    $str = stripslashes($str);
  }
  return mysqli_real_escape_string($link, $str);
}

//get member_id from session
$member_id = $_SESSION['SESS_MEMBER_ID'];

//checks whether the member has a billing address setup
//get the billing_id from the billing_details table based on the member_id in auth.php
$qry_select = mysqli_query($link, "SELECT * FROM billing_details WHERE member_id='$member_id'")
  or die("The system is experiencing technical issues.\n Our team is working on it.\nPlease try again after some few minutes.");

if (mysqli_num_rows($qry_select) > 0 && isset($_GET['id'])) {

  //get cart_id
  $id = mysqli_real_escape_string($link, $_GET['id']);

  //define default values for flag_0 and flag_1
  $flag_0 = 0;
  $flag_1 = 1;

  //retrive a timezone from the timezones table
  $timezones = mysqli_query($link, "SELECT * FROM timezones WHERE flag='$flag_1'")
    or die("Something is wrong. \n Our team is working on it at the moment.\n Please check back after some few minutes.");

  $row = mysqli_fetch_assoc($timezones); //gets retrieved row

  $active_reference = $row['timezone_reference']; //gets active timezone

  date_default_timezone_set($active_reference); //sets the default timezone for use

  $time_stamp = date("H:i:s"); //gets the current time

  $delivery_date = date("Y-m-d"); //gets the current date

  //storing the billing_id into a variable
  $row = mysqli_fetch_array($qry_select);
  $billing_id = $row['billing_id'];

  // ÎNCEPE TRANZACȚIA
  mysqli_begin_transaction($link);

  try {
    // Verifică dacă produsul există în coș și aparține utilizatorului
    $check_query = "SELECT cart_id FROM cart_details WHERE cart_id='$id' AND member_id='$member_id' AND flag='$flag_0'";
    $check_result = mysqli_query($link, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
      throw new Exception("Produsul nu există în coș sau este deja comandat.");
    }

    //Create INSERT query
    $qry_create = "INSERT INTO orders_details(member_id, billing_id, cart_id, delivery_date, flag, time_stamp)
                       VALUES('$member_id', '$billing_id', '$id', '$delivery_date', '$flag_0', '$time_stamp')";

    if (!mysqli_query($link, $qry_create)) {
      throw new Exception("Eroare la salvarea comenzii: " . mysqli_error($link));
    }

    //Create UPDATE query (updates flag value in the cart_details table)
    $qry_update = "UPDATE cart_details SET flag='$flag_1' WHERE cart_id='$id' AND member_id='$member_id'";

    if (!mysqli_query($link, $qry_update)) {
      throw new Exception("Eroare la actualizarea coșului: " . mysqli_error($link));
    }

    // Verifică dacă s-a actualizat cel puțin un rând
    if (mysqli_affected_rows($link) == 0) {
      throw new Exception("Nu s-a putut actualiza coșul. Verifică dacă produsul există.");
    }

    // COMMIT tranzacția
    mysqli_commit($link);

    // Redirecționează cu succes
    header("location: cont.php?success=1");
    exit;
  } catch (Exception $e) {
    // ROLLBACK în caz de eroare
    mysqli_rollback($link);

    // Redirecționează cu mesaj de eroare
    header("location: cont.php?error=" . urlencode($e->getMessage()));
    exit;
  }
} else {
  // Dacă nu are adresă de facturare, redirecționează
  if (mysqli_num_rows($qry_select) == 0) {
    header("location: billing-alternative.php");
  } else {
    header("location: cont.php?error=" . urlencode("ID invalid sau lipsește parametrul."));
  }
  exit;
}

//Închide conexiunea
mysqli_close($link);
