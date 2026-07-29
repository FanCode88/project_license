<?php
//Start session
session_start();

//checking connection and connecting to a database
require_once('connection/config.php');

//Connect to mysql server using modern MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

//Set charset to utf8mb4 for proper character encoding
mysqli_set_charset($link, "utf8mb4");

// check if the 'id' variable is set in URL
if (isset($_GET['id'])) {
    // get id value
    $id = $_GET['id'];

    // delete the entry using prepared statements to prevent SQL injection
    $stmt = mysqli_prepare($link, "DELETE FROM members WHERE member_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        // redirect back to the accounts page
        header("Location: accounts.php");
        exit();
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        die("The member does not exist ... \n");
    }
} else {
    mysqli_close($link);
    // if id isn't set, redirect back to the accounts page
    header("Location: accounts.php");
    exit();
}
