<?php
// Start session
session_start();

// Include database connection details
require_once('connection/config.php');

// Connect to MySQL server using MySQLi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$conn) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Setăm charset-ul pentru securitate și diacritice
mysqli_set_charset($conn, "utf8mb4");

// Function to sanitize values received from the form. Prevents SQL injection
function clean($conn, $str)
{
    $str = trim($str);
    return mysqli_real_escape_string($conn, $str);
}

// Sanitize the POST values (pasăm și conexiunea `$conn` pentru escape corect)
$StreetAddress = clean($conn, $_POST['sAddress']);
$BoxNo = clean($conn, $_POST['box']);
$City = clean($conn, $_POST['city']);
$MobileNo = clean($conn, $_POST['mNumber']);
$LandlineNo = clean($conn, $_POST['lNumber']);

// check if the 'id' variable is set in URL
if (isset($_GET['id'])) {
    // get id value and sanitize it
    $id = clean($conn, $_GET['id']);

    // Create INSERT query using MySQLi
    $qry = "INSERT INTO billing_details (member_id, Street_Address, P_O_Box_No, City, Mobile_No, Landline_No) VALUES ('$id', '$StreetAddress', '$BoxNo', '$City', '$MobileNo', '$LandlineNo')";

    if (mysqli_query($conn, $qry)) {
        // redirect to billing-success page
        header("Location: billing-success.php");
        exit();
    } else {
        die("Error executing query: " . mysqli_error($conn));
    }
} else {
    die("Adding billing information failed! Please try again after a few minutes.");
}
?>
