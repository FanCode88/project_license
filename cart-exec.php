<?php
// Start session
session_start();

// Include session details
require_once('auth.php');

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

// Checks if id is set in the url
if (isset($_GET['id'])) {
    // Retrieve the first quantity from the quantities table
    $quantities = mysqli_query($conn, "SELECT * FROM quantities")
        or die("Something is wrong ... \n" . mysqli_error($conn));
    $row = mysqli_fetch_assoc($quantities);
    $quantity_value = $row['quantity_value'];

    // Get id value and sanitize it
    $food_id = clean($conn, $_GET['id']);

    // Retrieve food_price from food_details based on $food_id
    $result = mysqli_query($conn, "SELECT * FROM food_details WHERE food_id='$food_id'")
        or die("A problem has occured ... \nOur team is working on it at the moment ... \nPlease check back after few hours.");
    $food_row = mysqli_fetch_assoc($result);
    $food_price = $food_row['food_price'];

    // Get member_id from session and sanitize it
    $member_id = clean($conn, $_SESSION['SESS_MEMBER_ID']);

    // Define default values for quantity, total, and flag_0
    $quantity_id = $row['quantity_id'];
    $total = $food_price * $quantity_value;
    $flag_0 = 0;

    // Create INSERT query using MySQLi
    $qry = "INSERT INTO cart_details (member_id, food_id, quantity_id, total, flag) VALUES ('$member_id', '$food_id', '$quantity_id', '$total', '$flag_0')";
    $insert_result = mysqli_query($conn, $qry);

    // Check whether the query was successful or not
    if ($insert_result) {
        header("Location: cart.php");
        exit();
    } else {
        die("A problem has occured with the system " . mysqli_error($conn));
    }
}
?>
