<?php
//Start session
session_start();

//Include database connection details
require_once('connection/config.php');

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Connect to mysql server using modern MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

//Function to sanitize values received from the form.
function clean($link, $str)
{
    $str = trim($str);
    return mysqli_real_escape_string($link, $str);
}

//Sanitize the POST values
$fname = clean($link, $_POST['fname']);
$lname = clean($link, $_POST['lname']);
$login = clean($link, $_POST['login']);
$password = $_POST['password']; // Se va aplica MD5 mai jos
$cpassword = $_POST['cpassword'];
$question_id = clean($link, $_POST['question']);
$answer = clean($link, $_POST['answer']);

//Check whether an account with a given email exists using prepared statement
$stmt_select = mysqli_prepare($link, "SELECT member_id FROM members WHERE login = ?");
mysqli_stmt_bind_param($stmt_select, "s", $login);
mysqli_stmt_execute($stmt_select);
mysqli_stmt_store_result($stmt_select);

if (mysqli_stmt_num_rows($stmt_select) > 0) {
    mysqli_stmt_close($stmt_select);
    mysqli_close($link);
    header("location: register-failed.php");
    exit();
}
mysqli_stmt_close($stmt_select);

// Hash passwords/answers using md5 as originally structured
$hashed_password = md5($password);
$hashed_answer = md5($answer);

// Create INSERT query using prepared statement for security
$stmt_insert = mysqli_prepare($link, "INSERT INTO members(firstname, lastname, login, passwd, question_id, answer) VALUES(?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt_insert, "ssssss", $fname, $lname, $login, $hashed_password, $question_id, $hashed_answer);
$result = mysqli_stmt_execute($stmt_insert);

// Check whether the query was successful or not
if ($result) {
    mysqli_stmt_close($stmt_insert);
    mysqli_close($link);
    header("location: register-success.php");
    exit();
} else {
    $error_msg = mysqli_error($link);
    mysqli_stmt_close($stmt_insert);
    mysqli_close($link);
    die("Something went wrong.\n Our team is working on it at the moment.\n Please try again after some few minutes.");
}
