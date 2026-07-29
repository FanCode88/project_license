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

//Function to sanitize values received from the form. Prevents SQL injection
function clean($link, $str) {
    $str = trim($str);
    return mysqli_real_escape_string($link, $str);
}

// check if Delete is set in POST
if (isset($_POST['Delete']) && isset($_POST['category'])) {
    // get id value of category and Sanitize the POST values
    $category_id = clean($link, $_POST['category']);

    // delete the entry using prepared statements
    $stmt = mysqli_prepare($link, "DELETE FROM categories WHERE category_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        // redirect back to options
        header("Location: options.php");
        exit();
    } else {
        $error = mysqli_error($link);
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        die("There was a problem while deleting the category ... \n" . $error);
    }
}
// check if the 'id' variable is set in URL (for direct link deletion like from categories.php)
elseif (isset($_GET['id'])) {
    // get id value
    $id = $_GET['id'];

    // delete the entry using prepared statements
    $stmt = mysqli_prepare($link, "DELETE FROM categories WHERE category_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        // redirect back to the categories
        header("Location: categories.php");
        exit();
    } else {
        $error = mysqli_error($link);
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        die("There was a problem while deleting the category ... \n" . $error);
    }
}
else {
    mysqli_close($link);
    // if neither is set, redirect back to options
    header("Location: options.php");
    exit();
}
