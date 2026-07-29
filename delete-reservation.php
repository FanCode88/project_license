<?php
// Start session (dacă nu e pornită deja prin auth.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('auth.php');
require_once('connection/config.php');

// Conexiune modernă MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Setăm charset-ul
mysqli_set_charset($link, "utf8mb4");

if (isset($_GET['id'])) {
    // Curățăm variabilele pentru securitate
    $id = mysqli_real_escape_string($link, trim($_GET['id']));
    $memberId = mysqli_real_escape_string($link, trim($_SESSION['SESS_MEMBER_ID']));

    // Executăm ștergerea folosind MySQLi
    $query = "DELETE FROM reservations_details WHERE ReservationID='$id' AND member_id='$memberId'";

    if (mysqli_query($link, $query)) {
        echo "success"; // Trimitem acest mesaj pentru ca JS să știe să șteargă rândul
    } else {
        echo "error: " . mysqli_error($link);
    }
}

mysqli_close($link);
?>
